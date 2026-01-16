@extends('layouts.app')

@section('title', 'Accueil - La Connaissance à votre portée')

@section('content')
<div class="bg-white text-gray-800">

    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gray-50 mix-blend-multiply"></div>
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob"></div>
            <div class="absolute -bottom-40 -left-20 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-2000"></div>
        </div>

        <div class="relative container mx-auto px-6 py-24 md:py-32 text-center">
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-extrabold tracking-tighter leading-tight animate-fade-in-down">
                Le savoir de demain, <br class="hidden md:block" />
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 to-purple-500">
                    accessible aujourd'hui.
                </span>
            </h1>
            <p class="mt-6 max-w-3xl mx-auto text-lg sm:text-xl text-gray-600 animate-fade-in-up animation-delay-300">
                Plongez dans un univers de connaissances avec des cours interactifs, des experts passionnés et une communauté dynamique.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4 animate-fade-in-up animation-delay-600">
                <a href="/courses" class="inline-block bg-indigo-600 text-white font-bold text-lg py-4 px-8 rounded-lg shadow-lg hover:bg-indigo-700 transform hover:-translate-y-1 transition-all duration-300">
                    Explorer les Cours
                </a>
                <a href="#features" class="inline-block bg-gray-200 text-gray-800 font-bold text-lg py-4 px-8 rounded-lg hover:bg-gray-300 transform hover:-translate-y-1 transition-all duration-300">
                    Découvrir plus
                </a>
            </div>
        </div>
    </div>

    <!-- Section "Pourquoi nous choisir ?" avec statistiques -->
    <section class="py-20 bg-gray-50" id="features">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold">Pourquoi notre plateforme est unique ?</h2>
                <p class="text-gray-600 mt-4 text-lg">Nous combinons technologie, expertise et support pour votre réussite.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 text-center">
                <div class="p-8 bg-white rounded-xl shadow-md animate-fade-in-up">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-indigo-100 text-indigo-500 mb-6 text-4xl font-bold">
                        50+
                    </div>
                    <h3 class="text-2xl font-semibold mb-2">Cours d'Experts</h3>
                    <p class="text-gray-500">Des formations conçues et animées par des professionnels reconnus dans leur domaine.</p>
                </div>
                <div class="p-8 bg-white rounded-xl shadow-md animate-fade-in-up animation-delay-200">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-purple-100 text-purple-500 mb-6 text-4xl font-bold">
                        10k+
                    </div>
                    <h3 class="text-2xl font-semibold mb-2">Étudiants Satisfaits</h3>
                    <p class="text-gray-500">Rejoignez une communauté de milliers d'apprenants qui nous font confiance.</p>
                </div>
                <div class="p-8 bg-white rounded-xl shadow-md animate-fade-in-up animation-delay-400">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 text-green-500 mb-6 text-4xl font-bold">
                        24/7
                    </div>
                    <h3 class="text-2xl font-semibold mb-2">Support & Accès</h3>
                    <p class="text-gray-500">Apprenez à votre rythme, où que vous soyez, avec un support toujours disponible.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Cours en Vedette -->
    <section class="py-20">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                <div class="lg:w-1/2">
                    <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600" alt="Cours de développement web" class="rounded-2xl shadow-2xl transform hover:scale-105 transition-transform duration-500">
                </div>
                <div class="lg:w-1/2">
                    <span class="text-indigo-600 font-semibold tracking-wide uppercase">Cours en vedette</span>
                    <h2 class="mt-2 text-3xl md:text-4xl font-bold leading-tight">Masterclass : Développement Web Moderne</h2>
                    <p class="mt-4 text-gray-600 text-lg">Devenez un développeur full-stack aguerri. Ce cours complet couvre les dernières technologies du front-end au back-end, avec des projets concrets.</p>
                    <ul class="mt-6 space-y-3">
                        <li class="flex items-center"><svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Apprentissage par projets</li>
                        <li class="flex items-center"><svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Accès à vie au contenu</li>
                        <li class="flex items-center"><svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Certificat de complétion</li>
                    </ul>
                    <a href="/courses/masterclass-web-dev" class="mt-8 inline-flex items-center bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-indigo-700 transition-colors duration-300 group">
                        Commencer l'aventure
                        <svg class="ml-2 h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Témoignages -->
    <section class="bg-gray-50 py-20">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold">Ils ont transformé leur carrière</h2>
                <p class="text-gray-600 mt-4 text-lg">Lisez les histoires de ceux qui ont atteint leurs objectifs grâce à nous.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <figure class="bg-white p-8 rounded-xl shadow-lg transform hover:-translate-y-2 transition-transform duration-300">
                    <blockquote class="text-lg text-gray-700 italic border-l-4 border-indigo-500 pl-6">
                        "La qualité des cours a dépassé toutes mes attentes. J'ai pu obtenir une promotion en moins de 6 mois !"
                    </blockquote>
                    <figcaption class="mt-6 flex items-center">
                        <img class="h-12 w-12 rounded-full object-cover" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Photo de Marie Dubois">
                        <div class="ml-4">
                            <div class="font-bold text-gray-900">Marie Dubois</div>
                            <div class="text-sm text-gray-500">Développeuse Front-end</div>
                        </div>
                    </figcaption>
                </figure>
                <figure class="bg-white p-8 rounded-xl shadow-lg transform hover:-translate-y-2 transition-transform duration-300">
                    <blockquote class="text-lg text-gray-700 italic border-l-4 border-indigo-500 pl-6">
                        "Une plateforme intuitive et des formateurs à l'écoute. C'est l'investissement le plus rentable que j'ai fait pour ma carrière."
                    </blockquote>
                    <figcaption class="mt-6 flex items-center">
                        <img class="h-12 w-12 rounded-full object-cover" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Photo de Jean Dupont">
                        <div class="ml-4">
                            <div class="font-bold text-gray-900">Jean Dupont</div>
                            <div class="text-sm text-gray-500">Chef de Projet Tech</div>
                        </div>
                    </figcaption>
                </figure>
                 <figure class="bg-white p-8 rounded-xl shadow-lg transform hover:-translate-y-2 transition-transform duration-300">
                    <blockquote class="text-lg text-gray-700 italic border-l-4 border-indigo-500 pl-6">
                        "Le support communautaire est incroyable. On ne se sent jamais seul face à un problème. Je recommande à 100%."
                    </blockquote>
                    <figcaption class="mt-6 flex items-center">
                        <img class="h-12 w-12 rounded-full object-cover" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Photo de Sarah Lemoine">
                        <div class="ml-4">
                            <div class="font-bold text-gray-900">Sarah Lemoine</div>
                            <div class="text-sm text-gray-500">UX/UI Designer</div>
                        </div>
                    </figcaption>
                </figure>
            </div>
        </div>
    </section>

    <!-- Section Call to Action Final -->
    <div class="py-24">
        <div class="container mx-auto px-6">
            <div class="relative bg-gradient-to-r from-purple-600 to-indigo-700 text-white text-center py-20 px-6 rounded-3xl shadow-2xl overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                <div class="relative">
                    <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight">Prêt à transformer votre avenir ?</h2>
                    <p class="mt-4 max-w-2xl mx-auto text-indigo-100 text-lg">Votre première leçon n'est qu'à un clic. Rejoignez-nous et libérez votre potentiel.</p>
                    <div class="mt-8">
                        <a href="/register" class="inline-block bg-white text-indigo-600 font-extrabold text-lg py-4 px-10 rounded-lg hover:bg-gray-100 transform hover:scale-110 transition-all duration-300 shadow-xl">
                            Créer mon compte gratuit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection