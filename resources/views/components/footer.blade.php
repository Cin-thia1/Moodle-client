<footer class="border-t border-gray-200 bg-white mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- About Section -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-4">LearnPlatform</h3>
                <p class="text-sm text-gray-600">
                    Une plateforme d'apprentissage collaborative pour étudiants et formateurs.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Navigation</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Tableau de bord</a></li>
                    <li><a href="{{ url('/courses') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Cours</a></li>
                    <li><a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Mon profil</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Ressources</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('about') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">À propos</a></li>
                    <li><a href="{{ route('contact.index') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Contact</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Aide</a></li>
                </ul>
            </div>

            <!-- Connect -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Nous suivre</h3>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-600 hover:text-indigo-600 transition-colors" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-indigo-600 transition-colors" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-indigo-600 transition-colors" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-600 text-center md:text-left">
                    &copy; {{ date('Y') }} LearnPlatform. Tous droits réservés.
                </p>
                <div class="flex gap-6 mt-4 md:mt-0">
                    <a href="#" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Politique de confidentialité</a>
                    <a href="#" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Conditions d'utilisation</a>
                </div>
            </div>
        </div>
    </div>
</footer>
