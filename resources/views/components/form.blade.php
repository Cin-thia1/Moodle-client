<!-- resources/views/components/form.blade.php -->
<form action="{{ $action }}" method="POST">
    @csrf
    @if(isset($method)) @method($method) @endif
    
    <x-input label="Name" name="name" value="{{ $value ?? '' }}" required />
    <x-input label="Due Date" name="duedate" type="datetime-local" value="{{ $value ?? '' }}" required />
    <x-input label="Attempt Number" name="attemptnumber" type="number" value="{{ $value ?? '' }}" required />
    
    <div class="mb-3">
        <label for="module_id" class="form-label">Module</label>
        <select name="module_id" id="module_id" class="form-control" required>
            @foreach($modules as $module)
                <option value="{{ $module->id }}" {{ (isset($moduleId) && $moduleId == $module->id) ? 'selected' : '' }}>
                    {{ $module->name }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
</form>
