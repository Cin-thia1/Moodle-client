<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Section;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Services\MoodleModuleService;

class ModuleController extends Controller
{
    protected $moodleModuleService;

    public function __construct(MoodleModuleService $moodleModuleService)
    {
        $this->moodleModuleService = $moodleModuleService;
    }

    public function index()
    {
        $modules = Module::with('section')->get();
        
        return view('modules.index', compact('modules'));
    }

    public function create(Request $request)
    {
        $sectionId = $request->get('section_id');
        $sections = Section::all();
        return view('modules.create', compact('sections', 'sectionId'));
    }

public function store(Request $request)
{
    // // Log des données reçues avant validation
    // \Log::info('ModuleController: données reçues', $request->all());
    
    try {
        // Validation des données
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'modname' => ['required', 'string', 'max:255'],
            'modplural' => ['required', 'string', 'max:255'],
            'file_path' => ['required', 'file', 'max:10240'], // max 10 Mo
            'section_id' => ['required', 'exists:sections,id'],
            'downloadcontent' => ['required', 'integer', 'in:0,1'],
        ]);
        
        // \Log::info('ModuleController: validation réussie', $validated);
        
        // Stockage simple du fichier
        if ($request->hasFile('file_path')) {
            // \Log::info('ModuleController: fichier détecté', [
            //     'original_name' => $request->file('file_path')->getClientOriginalName(),
            //     'mime_type' => $request->file('file_path')->getMimeType(),
            //     'size' => $request->file('file_path')->getSize()
            // ]);
            
            try {
                $filePath = $request->file('file_path')->store('pdfs', 'public');
                $validated['file_path'] = $filePath;
                //\Log::info('ModuleController: fichier stocké avec succès', ['file_path' => $filePath]);
            } catch (\Exception $e) {
                // \Log::error('ModuleController: erreur lors du stockage du fichier', [
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
                // ]);
                return redirect()->back()->withErrors('Erreur lors du téléchargement du fichier: ' . $e->getMessage());
            }
        } else {
           // \Log::warning('ModuleController: aucun fichier détecté malgré la validation');
        }
        
        // Création du module
        try {
            $module = Module::create($validated);
           // \Log::info('ModuleController: module créé avec succès', ['module_id' => $module->id]);
            
            // Log du moodle_id de la section et du cours
            $section = Section::find($validated['section_id']);
            if ($section) {
                $course = $section->course;
                // \Log::info('ModuleController: informations contextuelles', [
                //     'section_id' => $section->id,
                //     'section_moodle_id' => $section->moodle_id,
                //     'course_id' => $course ? $course->id : null,
                //     'course_moodle_id' => $course ? $course->moodle_id : null
                // ]);
            }
            
        } catch (\Exception $e) {
            // \Log::error('ModuleController: erreur lors de la création du module', [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);
            return redirect()->back()->withErrors('Erreur lors de la création du module: ' . $e->getMessage());
        }
        
        // Log de création via service Moodle
        try {
            $this->moodleModuleService->logModuleCreation($module);
            //\Log::info('ModuleController: log Moodle créé avec succès');
        } catch (\Exception $e) {
            // \Log::error('ModuleController: erreur lors de la création du log Moodle', [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);
            // On continue même si le log Moodle échoue
        }
        
        // Vérifie que la relation section existe avant d'accéder à course_id
        if (!$module->relationLoaded('section')) {
            $module->load('section');
            //\Log::info('ModuleController: relation section chargée');
        }
        
        $courseId = optional($module->section)->course_id;
        //\Log::info('ModuleController: courseId récupéré', ['course_id' => $courseId]);
        
        // Sécurise la redirection même si le cours n'existe pas
        if ($courseId) {
            //\Log::info('ModuleController: redirection vers la page du cours', ['course_id' => $courseId]);
            return redirect()
                ->route('courses.show', ['course' => $courseId])
                ->with('success', 'Module créé avec succès.');
        }
        
        //\Log::warning('ModuleController: cours associé introuvable');
        return redirect()->back()->withErrors('Le cours associé est introuvable.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::warning('ModuleController: erreur de validation', [
            'errors' => $e->errors()
        ]);
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        \Log::error('ModuleController: erreur inattendue', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()->withErrors('Une erreur inattendue s\'est produite: ' . $e->getMessage());
    }
}


    public function show(Module $module)
    {
        return view('modules.show', compact('module'));
    }

    public function edit(Module $module)
    {
        $sections = Section::all();
        return view('modules.edit', compact('module', 'sections'));
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'modname' => 'required|string|max:255',
            'modplural' => 'required|string|max:255',
            'downloadcontent' => 'required|boolean',
            'file_path' => 'nullable|string|max:255',
            'section_id' => 'required|exists:sections,id',
        ]);

        $module->update($validated);
        $this->moodleModuleService->logModuleUpdate($module);

        return redirect()->route('modules.index')->with('success', 'Module mis à jour avec succès.');
    }

    public function destroy(Module $module)
    {
        $this->moodleModuleService->logModuleDeletion($module);
        $module->delete();

        return redirect()->route('modules.index')->with('success', 'Module supprimé avec succès.');
    }

public function download(Module $module)
{
    // Si c'est un fichier Moodle
    if ($module->moodle_id) {
        return redirect($module->file_path);
    }
    
    // Si c'est un fichier local
    return response()->download(storage_path('app/public/' . $module->file_path));
}
}
