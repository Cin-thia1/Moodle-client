# Modèle Logique de Données (MLD)

## Tables et Colonnes avec commentaires

### 1. **users**
- `id` (PK, auto-incrément)  
  *Identifiant unique pour chaque utilisateur.*
- `username` (string)  
  *Nom d'utilisateur utilisé pour se connecter.*
- `password` (string)  
  *Mot de passe haché pour sécuriser les comptes.*
- `token` (string, nullable)  
  *Jeton d'authentification pour les connexions ou les sessions persistantes.*
- `picture` (string, nullable)  
  *Chemin ou URL de la photo de profil de l'utilisateur.*
- `role` (string)  
  *Rôle de l'utilisateur dans le système (exemple : "admin", "teacher", "student").*

---

### 2. **courses**
- `id` (PK, auto-incrément)  
  *Identifiant unique pour chaque cours.*
- `fullname` (string)  
  *Nom complet du cours (exemple : "Introduction à la programmation").*
- `shortname` (string)  
  *Nom abrégé du cours (exemple : "IntroProg").*
- `summary` (text, nullable)  
  *Résumé ou description du cours (facultatif).*
- `numsections` (integer)  
  *Nombre de sections ou de chapitres dans le cours.*
- `startdate` (timestamp)  
  *Date de début du cours.*
- `enddate` (timestamp, nullable)  
  *Date de fin du cours (facultatif).*
- `created_at` (timestamp)  
  *Date de création de l'enregistrement.*
- `updated_at` (timestamp)  
  *Date de dernière mise à jour de l'enregistrement.*
- `teacher_id` (FK -> users.id)  
  *Référence au professeur responsable du cours.*

---

### 3. **sections**
- `id` (PK, auto-incrément)  
  *Identifiant unique pour chaque section.*
- `name` (string)  
  *Nom de la section ou du chapitre (exemple : "Chapitre 1 : Les bases").*
- `course_id` (FK -> courses.id)  
  *Référence au cours auquel appartient cette section.*

---

### 4. **modules**
- `id` (PK, auto-incrément)  
  *Identifiant unique pour chaque module.*
- `name` (string)  
  *Nom du module (exemple : "Quiz 1").*
- `modname` (string)  
  *Nom du type de module (exemple : "quiz", "assignment").*
- `modplural` (string)  
  *Nom au pluriel du type de module (exemple : "quizzes", "assignments").*
- `downloadcontent` (boolean)  
  *Indique si le contenu du module est téléchargeable (true/false).*
- `file_path` (string)  
  *Chemin vers les fichiers associés au module (exemple : syllabus ou documents).*
- `section_id` (FK -> sections.id)  
  *Référence à la section à laquelle appartient ce module.*

---

### 5. **assignments**
- `id` (PK, auto-incrément)  
  *Identifiant unique pour chaque devoir.*
- `name` (string)  
  *Nom du devoir (exemple : "Devoir 1").*
- `duedate` (timestamp)  
  *Date limite pour rendre le devoir.*
- `attemptnumber` (integer)  
  *Nombre maximum de tentatives autorisées pour ce devoir.*
- `module_id` (FK -> modules.id)  
  *Référence au module auquel appartient ce devoir.*

---

### 6. **submissions**
- `id` (PK, auto-incrément)  
  *Identifiant unique pour chaque soumission d'un devoir.*
- `status` (string)  
  *Statut de la soumission (exemple : "en attente", "soumis").*
- `created_at` (timestamp)  
  *Date de création de la soumission.*
- `updated_at` (timestamp)  
  *Date de dernière mise à jour de la soumission.*
- `file_path` (string)  
  *Chemin vers le fichier soumis par l'étudiant.*
- `assignment_id` (FK -> assignments.id)  
  *Référence au devoir soumis.*
- `student_id` (FK -> users.id)  
  *Référence à l'étudiant qui a soumis le devoir.*

---

### 7. **grades**
- `id` (PK, auto-incrément)  
  *Identifiant unique pour chaque note attribuée.*
- `grade` (integer)  
  *Note attribuée à une soumission (exemple : 85/100).*
- `comment` (text, nullable)  
  *Commentaire facultatif sur la soumission ou la note.*
- `created_at` (timestamp)  
  *Date de création de l'évaluation.*
- `updated_at` (timestamp)  
  *Date de dernière mise à jour de l'évaluation.*
- `submission_id` (FK -> submissions.id)  
  *Référence à la soumission évaluée.*
- `teacher_id` (FK -> users.id)  
  *Référence à l'enseignant qui a attribué la note.*

---
