@extends('layouts.app')

@section('title', 'À Propos de Nous')

@section('content')
<div class="bg-gray-50">
    <!-- Section Héros -->
    <div class="relative bg-white pt-16 pb-20 lg:pt-24 lg:pb-28">
        <div class="relative container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200 flex items-center justify-center gap-2 mb-6">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight">
                    Notre Histoire, Votre Avenir
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-600">
                    Bienvenue sur notre plateforme. Découvrez qui nous sommes, ce qui nous motive, et comment nous nous engageons à faire de votre apprentissage une expérience exceptionnelle.
                </p>
            </div>
        </div>
    </div>

    <!-- Section Mission et Valeurs -->
    <div class="py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base font-semibold text-indigo-600 tracking-wider uppercase">Notre Mission</h2>
                <p class="mt-2 text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                    Rendre le savoir accessible et inspirant.
                </p>
                <p class="mt-5 max-w-prose mx-auto text-xl text-gray-500">
                    Nous croyons que l'éducation a le pouvoir de transformer des vies. Notre mission est de briser les barrières de l'apprentissage en offrant des outils de qualité, un contenu pertinent et une communauté de soutien.
                </p>
            </div>

            <div class="mt-16 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                <div class="text-center">
                    <div class="flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 text-indigo-600 mx-auto">
                        <i class="fas fa-universal-access text-2xl"></i>
                    </div>
                    <h3 class="mt-5 text-xl font-bold text-gray-900">Accessibilité</h3>
                    <p class="mt-2 text-base text-gray-600">Un design intuitif et des ressources disponibles pour tous, partout et à tout moment.</p>
                </div>
                <div class="text-center">
                    <div class="flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 text-indigo-600 mx-auto">
                        <i class="fas fa-lightbulb text-2xl"></i>
                    </div>
                    <h3 class="mt-5 text-xl font-bold text-gray-900">Innovation</h3>
                    <p class="mt-2 text-base text-gray-600">Nous intégrons constamment les dernières technologies pédagogiques pour un apprentissage interactif.</p>
                </div>
                <div class="text-center">
                    <div class="flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 text-indigo-600 mx-auto">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <h3 class="mt-5 text-xl font-bold text-gray-900">Communauté</h3>
                    <p class="mt-2 text-base text-gray-600">Un espace d'échange et de collaboration où étudiants et enseignants peuvent grandir ensemble.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Équipe (actuellement commentée, vous pouvez la réactiver) --}}
    {{--
    <div class="bg-white py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Notre Équipe</h2>
            <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-500">Les passionnés qui rendent tout cela possible.</p>
            <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-12">
                <div class="space-y-4">
                    <img class="mx-auto h-40 w-40 rounded-full object-cover shadow-lg" src="{{ asset('images/team/jean.jpg') }}" alt="Jean Dupont">
                    <div class="space-y-1">
                        <h3 class="text-xl font-bold">Jean Dupont</h3>
                        <p class="text-indigo-600 font-semibold">Fondateur & CEO</p>
                    </div>
                </div>
                <!-- ... autres membres ... -->
            </div>
        </div>
    </div>
    --}}

    <!-- Section Contact (CTA) -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-center py-16 px-6 rounded-2xl shadow-2xl">
            <h2 class="text-4xl font-extrabold tracking-tight">Prêt à nous rejoindre ?</h2>
            <p class="mt-4 max-w-2xl mx-auto text-indigo-100">Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            <div class="mt-8">
                <a href="mailto:ouendeufranck@gmail.com" class="inline-block bg-white text-indigo-600 font-bold text-lg py-3 px-8 rounded-lg hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-md">
                    Contactez-nous
                </a>
            </div>
        </div>
    </div>
</div>
@endsection