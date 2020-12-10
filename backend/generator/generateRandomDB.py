import sqlite3
from random import randint, shuffle
import json
from os import path, remove

if path.exists('../px.db'):
    if input('Do you really want to re-create the database? (y/n): ').strip().lower() == 'y':
        remove('../px.db')
    else:
        exit(-1)

conn = sqlite3.connect("../px.db")
cursor = conn.cursor()

print('Creating...')

# CREATE
cursor.execute('CREATE TABLE institutes (id INTEGER PRIMARY KEY, name TEXT unique);')
cursor.execute(
    'CREATE TABLE groups     (id INTEGER PRIMARY KEY, name TEXT unique, i_id INTEGER, FOREIGN KEY (i_id) REFERENCES institutes(id));')
cursor.execute(
    'CREATE TABLE students   (id INTEGER PRIMARY KEY, surname TEXT NOT NULL DEFAULT "", name TEXT NOT NULL DEFAULT "", patronymic TEXT NOT NULL DEFAULT "", username TEXT DEFAULT NULL, password TEXT, group_id INTEGER, FOREIGN KEY (group_id) REFERENCES groups(id));')
cursor.execute(
    'CREATE TABLE teachers   (id INTEGER PRIMARY KEY, surname TEXT NOT NULL DEFAULT "", name TEXT NOT NULL DEFAULT "", patronymic TEXT NOT NULL DEFAULT "", username TEXT DEFAULT NULL, password TEXT);')
cursor.execute(
    'CREATE TABLE marks      (id INTEGER PRIMARY KEY, subject_id INTEGER NOT NULL, mark TEXT NOT NULL, student_id INTEGER NOT NULL, event INTEGER NOT NULL, FOREIGN KEY (student_id) REFERENCES students(id), FOREIGN KEY (subject_id) REFERENCES subjects(id));')
cursor.execute(
    'CREATE TABLE subjects   (id INTEGER PRIMARY KEY, name TEXT NOT NULL, group_id INTEGER NOT NULL, host_id INTEGER NOT NULL, semester INTEGER NOT NULL, pass_type TEXT NOT NULL, FOREIGN KEY (host_id) REFERENCES teachers(id), FOREIGN KEY (group_id) REFERENCES groups(id));')

conn.commit()

print('Institutes...')
# institutes
cursor.execute(f'INSERT INTO institutes (name) VALUES ("Информационных технологий");')
cursor.execute(f'INSERT INTO institutes (name) VALUES ("Кибернетики");')
cursor.execute(f'INSERT INTO institutes (name) VALUES ("Физико-технических наук");')
cursor.execute(f'INSERT INTO institutes (name) VALUES ("Гуманитарных наук");')
conn.commit()

print('Groups...')
# groups

# it
for i in range(1, 5):
    cursor.execute(f'INSERT INTO groups (name, i_id) VALUES ("ИКБО-{i}-19", 1);')

# kibs
for i in range(1, 5):
    cursor.execute(f'INSERT INTO groups (name, i_id) VALUES ("КИБ-{i}-19", 2);')

# FTS
for i in range(1, 5):
    cursor.execute(f'INSERT INTO groups (name, i_id) VALUES ("ФТИ-{i}-19", 3);')

# HS
for i in range(1, 5):
    cursor.execute(f'INSERT INTO groups (name, i_id) VALUES ("ГУМ-{i}-19", 4);')

conn.commit()

print('Students...')
# students

names = json.load(open('russian_names.json', 'r', encoding='UTF-8'))
surnames = json.load(open('russian_surnames.json', 'r', encoding='utf-8-sig'))


def getRandomSNP(count=1):
    shuffle(names)
    shuffle(surnames)

    for i in range(count):
        yield names[i]['Name'], surnames[i]['Surname'] if names[i]['Sex'] == 'М' else surnames[i]['Surname'] + 'а', 'Павлович' if names[i]['Sex'] == 'М' else 'Павловна'


def snpRoutine(i):
    value = randint(15, 40)
    nGen = getRandomSNP(value)
    for _ in range(value):
        for name, surname, patronymic in nGen:
            cursor.execute(f'''INSERT INTO students (name, surname, patronymic, password, group_id) 
                                   VALUES ("{name}", "{surname}", "{patronymic}", "$2y$10$un0j5qOX/GXae4ZWuk6RYumqs7bD//MNAzVvwUIAna2PC/kFawhMS", {i})''')
            break


# it
for i in range(1, 5):
    snpRoutine(i)

print('Students [IT] done')

# kibs
for i in range(5, 11):
    snpRoutine(i)

print('Students [KIB] done')

# FTS
for i in range(11, 17):
    snpRoutine(i)

print('Students [FTS] done')

# HS
for i in range(17, 22):
    snpRoutine(i)

conn.commit()
print('Students [HS] done')

# teachers
for _ in range(15):
    nGen = getRandomSNP(15)
    for name, surname, patronymic in nGen:
        cursor.execute(f'''INSERT INTO teachers (name, surname, patronymic, password) 
                                           VALUES ("{name}", "{surname}", "{patronymic}", "$2y$10$8LGYm0dXxSwgl42uHJvxXuryY8ms0rgYoQSZoYJPQhNJc2oUT/Zw.")''')
        break

print('Teachers done')

print('Subjects...')
# subjects
it_subjs = ['Математический анализ', 'Линейная алгебра', 'ООП', 'Вычислительная математика', 'Физика',
            'Физическая культура', 'Процедрурное программирование', 'Информатика', 'Философия', 'Базы данных']
kib_subjs = ['Математический анализ', 'Линейная алгебра', 'Вычислительная математика', 'Физика', 'Физическая культура',
             'Информатика', 'Философия', 'Анализ высоковычслительных систем', 'Инофрмационная безопасность',
             'Криптография', 'Стеганография', 'Реверс-инжениринг']
fts_subjs = ['Математический анализ', 'Линейная алгебра', 'Физика', 'Физическая культура', 'Информатика', 'Философия',
             'Физика сверхультрапродвинутый курс', 'Цветмет', 'Серометху**', 'Основы оптики для собак']
hs_subjs = ['Физическая культура', 'Философия', 'Бизнес-литература', 'Зарубежная литература', 'Иностранный язык',
            'Основы торговли в переходах', 'Выживание в условиях низкой зарплаты', 'Основы правописания букв в словах']


def subjRoutine(subj, rang):
    for i in range(*rang):
        for j in range(1, 4):
            cursor.execute(
                f'''INSERT INTO subjects (name, group_id, host_id, semester, pass_type) VALUES ("{subj}", {i}, {randint(1, 15)}, {j}, "{'Экзамен' if randint(0, 1) == 1 else 'Зачет'}");''')


# it
for subj in it_subjs:
    subjRoutine(subj, (1, 5))

print('Subjects [IT] done')

# kib
for subj in kib_subjs:
    subjRoutine(subj, (5, 11))

print('Subjects [KIB] done')

# fts
for subj in fts_subjs:
    subjRoutine(subj, (11, 17))

print('Subjects [FTS] done')

# hs
for subj in hs_subjs:
    subjRoutine(subj, (17, 22))

print('Subjects [HS] done')

conn.commit()
print('Subjects done')

print('Marks...')
# I AM EXTRA LAZY PERSON. JUST SUFFER
for i in range(1, cursor.execute('select count(1) from students;').fetchone()[0] + 1):
    for j in range(1, cursor.execute('select count(1) from subjects;').fetchone()[0] + 1):
        cursor.execute(
            f'''INSERT INTO marks (subject_id, mark, student_id, event) VALUES ({j}, {randint(1, 5)}, {i}, 0);''')
conn.commit()
print('Marks done')