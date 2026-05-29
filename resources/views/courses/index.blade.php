@extends('layouts.app')

@section('title', 'Mes Cours')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- En-tête de la page -->
        <header class="mb-10">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <!-- Titre et bouton de retour -->
                <div>
                    <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200 flex items-center gap-2 mb-3">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight flex items-center gap-4">
                        <i class="fas fa-school text-indigo-500"></i> Espace Cours
                    </h1>
                </div>

                <!-- Actions pour l'administrateur ou le professeur -->
                @if(auth()->user()->hasRole('ROLE_TEACHER') || auth()->user()->hasRole('ROLE_ADMIN'))
                    <div class="flex items-center gap-3">
                        <a href="{{ route('courses.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all duration-300">
                            <i class="fas fa-plus-circle"></i>
                            Créer un nouveau cours
                        </a>
                        <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 font-semibold py-3 px-6 rounded-lg shadow-sm border-2 border-indigo-200 hover:bg-indigo-50 hover:border-indigo-300 transform hover:-translate-y-0.5 transition-all duration-300">
                            <i class="fas fa-th-large"></i>
                            Gérer les catégories
                        </a>
                    </div>
                @endif
            </div>
        </header>

        <!-- Barre de recherche -->
        <div class="mb-12 bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <form action="{{ route('courses.index') }}" method="GET">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search"
                           class="block w-full py-3 pl-12 pr-4 text-lg text-gray-900 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                           placeholder="Rechercher un cours par nom, catégorie..."
                           value="{{ request()->get('search') }}">
                    <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-6 bg-indigo-600 text-white font-semibold rounded-r-xl hover:bg-indigo-700 transition-colors">
                        Rechercher
                    </button>
                </div>
            </form>
        </div>

        <!-- Section "Mes Cours" pour les étudiants et enseignants -->
        @if(auth()->user()->hasRole('ROLE_STUDENT'))
            <!-- Pour les étudiants : Cours auxquels il est inscrit -->
            <section class="mb-16">
                <div class="mb-6 border-b-2 border-gray-200 pb-3">
                    <h2 class="text-3xl font-bold text-gray-800">Vos cours inscrits</h2>
                    <p class="text-gray-500 mt-1">Reprenez là où vous vous êtes arrêté.</p>
                </div>
                @if(isset($enrolledCourses))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @forelse($enrolledCourses as $course)
                        <x-course :course="$course" />
                    @empty
                        <div class="col-span-full bg-white text-center p-12 rounded-2xl shadow-sm border border-gray-200">
                            <i class="fas fa-book-reader text-5xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700">Vous n'êtes inscrit à aucun cours pour le moment.</h3>
                            <p class="text-gray-500 mt-2">Explorez les cours disponibles ci-dessous pour commencer votre apprentissage !</p>
                        </div>
                    @endforelse
                </div>
                @endif
            </section>

            <!-- Pour les étudiants : Cours disponibles (non inscrits) -->
            <section class="mb-16">
                <div class="mb-6 border-b-2 border-gray-200 pb-3">
                    <h2 class="text-3xl font-bold text-gray-800">Cours disponibles</h2>
                    <p class="text-gray-500 mt-1">Découvrez de nouvelles compétences à acquérir.</p>
                </div>
                @if(isset($availableCourses))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @forelse($availableCourses as $course)
                        <x-course :course="$course" />
                    @empty
                        <div class="col-span-full bg-white text-center p-12 rounded-2xl shadow-sm border border-gray-200">
                            <i class="fas fa-layer-group text-5xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700">Vous êtes inscrit à tous les cours disponibles.</h3>
                            <p class="text-gray-500 mt-2">Bien joué ! Revenez bientôt pour découvrir nos nouveautés.</p>
                        </div>
                    @endforelse
                </div>
                @endif
            </section>

        @elseif(auth()->user()->hasRole('ROLE_TEACHER') || auth()->user()->hasRole('ROLE_ADMIN'))
            <!-- Pour les enseignants/admins : Cours créés -->
            <section class="mb-16">
                <div class="mb-6 border-b-2 border-gray-200 pb-3">
                    <h2 class="text-3xl font-bold text-gray-800">
                        @if(auth()->user()->hasRole('ROLE_TEACHER'))
                            Vos cours
                        @else
                            Tous les cours
                        @endif
                    </h2>
                    <p class="text-gray-500 mt-1">
                        @if(auth()->user()->hasRole('ROLE_TEACHER'))
                            Gérez vos cours et suivez vos étudiants.
                        @else
                            Explorez tous les cours du système.
                        @endif
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @forelse($courses as $course)
                        <x-course :course="$course" />
                    @empty
                        <div class="col-span-full bg-white text-center p-12 rounded-2xl shadow-sm border border-gray-200">
                            <i class="fas fa-book-reader text-5xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700">
                                @if(auth()->user()->hasRole('ROLE_TEACHER'))
                                    Vous n'avez pas encore créé de cours.
                                @else
                                    Aucun cours disponible.
                                @endif
                            </h3>
                            <p class="text-gray-500 mt-2">
                                @if(auth()->user()->hasRole('ROLE_TEACHER'))
                                    Créez votre premier cours pour commencer à enseigner !
                                @else
                                    Revenez bientôt.
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Pour les enseignants : Section "Tous les cours du système" -->
            @if(auth()->user()->hasRole('ROLE_TEACHER') && isset($allCourses))
            <section class="mb-16">
                <div class="mb-6 border-b-2 border-gray-200 pb-3">
                    <h2 class="text-3xl font-bold text-gray-800">
                        <i class="fas fa-globe text-indigo-500"></i> Tous les cours du système
                    </h2>
                    <p class="text-gray-500 mt-1">Explorez tous les cours disponibles dans le système.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @forelse($allCourses as $course)
                        <div class="relative">
                            <x-course :course="$course" />
                            @if($course->teacher_id == auth()->user()->id)
                                <div class="absolute top-3 left-3 bg-indigo-600 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-sm">
                                    Votre cours
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full bg-white text-center p-12 rounded-2xl shadow-sm border border-gray-200">
                            <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700">Aucun cours disponible.</h3>
                            <p class="text-gray-500 mt-2">Revenez bientôt.</p>
                        </div>
                    @endforelse
                </div>
            </section>
            @endif

        @else
            <!-- Pour les autres rôles -->
            <section class="mb-16">
                <div class="mb-6 border-b-2 border-gray-200 pb-3">
                    <h2 class="text-3xl font-bold text-gray-800">Tous les cours</h2>
                    <p class="text-gray-500 mt-1">Explorez tous les cours disponibles.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @forelse($courses as $course)
                        <x-course :course="$course" />
                    @empty
                        <div class="col-span-full bg-white text-center p-12 rounded-2xl shadow-sm border border-gray-200">
                            <i class="fas fa-book-reader text-5xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700">Aucun cours disponible.</h3>
                            <p class="text-gray-500 mt-2">Revenez bientôt.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        @endif

    </div>
</div>
@endsection