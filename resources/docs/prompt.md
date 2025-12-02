You said:
etant donne cette modelisation,"
# Modèle Logique de Données (MLD)

## Tables et Colonnes

### 1. **users**
- id (PK, auto-incrément)
- username (string)
- password (string)
- token (string, nullable)
- picture (string, nullable)
- role (string)

---

### 2. **courses**
- id (PK, auto-incrément)
- fullname (string)
- shortname (string)
- summary (text, nullable)
- numsections (integer)
- startdate (timestamp)
- enddate (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- teacher_id (FK -> users.id)

---

### 3. **sections**
- id (PK, auto-incrément)
- name (string)
- course_id (FK -> courses.id)

---

### 4. **modules**
- id (PK, auto-incrément)
- name (string)
- modname (string)
- modplural (string)
- downloadcontent (boolean)
- file_path: (string)
- section_id (FK -> sections.id)

---

### 5. **assignments**
- id (PK, auto-incrément)
- name (string)
- duedate (timestamp)
- attemptnumber (integer)
- module_id (FK -> modules.id)

---

### 6. **submissions**
- id (PK, auto-incrément)
- status (string)
- created_at (timestamp)
- updated_at (timestamp)
- file_path: (string)
- assignment_id (FK -> assignments.id)
- student_id (FK -> users.id)


---

### 7. **grades**
- id (PK, auto-incrément)
- grade (integer)
- comment (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- submission_id (FK -> submissions.id)
- teacher_id (FK -> users.id)

---
"
j'ai deja ecrit les migration et les modeles, il me reste les routes et les controllers.
tu vas m'aider a creer ces controller, pour une application laravel-blade, docn pour chaque entite, tu me donne:
-le code du conroller
-les routes a coller dans web.php
-la commande pour creer les vues: "mkdir -p resources/views/example
touch resources/views/example/{index,create,edit,show}.blade.php"

-le code source complet des vues index,show,edit,create pour tester le controller

on iraa classe par classe, a chaque fois tu attendra le modele, j'ai gere mon authentification avec breeze.

continuons par ce modele:""
donne moi ce que je t'ai demande et donne le code complet partout.