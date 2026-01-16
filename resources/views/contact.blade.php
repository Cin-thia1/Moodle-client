@extends('layouts.app')

@section('title', 'Contactez-nous')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-16 gap-y-12">

            <!-- Colonne de gauche : Informations -->
            <div class="lg:pr-8">
                <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200 flex items-center gap-2 mb-8">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight">
                    Entrons en contact.
                </h1>
                <p class="mt-4 text-lg text-gray-600 leading-relaxed">
                    Une question, une suggestion ou besoin d'aide ? Notre équipe est à votre écoute. Remplissez le formulaire ou utilisez nos coordonnées directes.
                </p>

                <div class="mt-10 space-y-6 border-t border-gray-200 pt-8">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 flex items-center justify-center h-12 w-12 rounded-lg bg-indigo-50 text-indigo-600">
                            <i class="fas fa-envelope text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Email</h3>
                            <p class="text-gray-600">Pour toute demande générale.</p>
                            <a href="mailto:ouendeufranck@gmail.com" class="mt-1 text-indigo-600 font-medium hover:underline">ouendeufranck@gmail.com</a>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 flex items-center justify-center h-12 w-12 rounded-lg bg-indigo-50 text-indigo-600">
                            <i class="fas fa-phone-alt text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Téléphone</h3>
                            <p class="text-gray-600">Du lundi au vendredi, de 9h à 17h.</p>
                            <a href="tel:+23765054413" class="mt-1 text-indigo-600 font-medium hover:underline">+237 650 54 44 13</a>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 flex items-center justify-center h-12 w-12 rounded-lg bg-indigo-50 text-indigo-600">
                            <i class="fas fa-map-marker-alt text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Adresse</h3>
                            <p class="text-gray-600">ENSPY, 8390 Yaoundé-Cameroun</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite : Formulaire -->
            <div class="bg-white p-8 lg:p-12 rounded-2xl shadow-xl border border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Envoyez-nous un message</h2>
                <form action="{{ route('contact.send') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Votre nom</label>
                        <input type="text" id="name" name="name" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm py-3 px-4 focus:border-indigo-500 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Votre email</label>
                        <input type="email" id="email" name="email" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm py-3 px-4 focus:border-indigo-500 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea id="message" name="message" rows="5" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm py-3 px-4 focus:border-indigo-500 focus:ring-indigo-500 transition" placeholder="Comment pouvons-nous vous aider ?"></textarea>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span>Envoyer le message</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection