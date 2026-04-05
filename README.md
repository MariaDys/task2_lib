# Задание 2. Выбор библиотеки

Вариант: [5, 7] — php, запуск системных процессов. Предусловия: входные данные получаются от недоверенных пользователей.


## 1. Определение критериев

Буду ссылаться на конкретные проверки (критерии), описанные в репозитории проекта OpenSSF Scorecard https://github.com/ossf/scorecard/blob/main/docs/checks.md и в руководстве Concise Guide for Evaluating Open Source Software https://best.openssf.org/Concise-Guide-for-Evaluating-Open-Source-Software.html 

### Критерий 1. Активность сопровождения проекта (Maintained)

Ссылка: https://github.com/ossf/scorecard/blob/main/docs/checks.md#maintained

По Scorecard:

Если проекту больше 90 дней, то необходимо оценить, ведется ли работа по его сопровождению. Положительно оцениваются проекты с >=1 коммитом в неделю за последние 90 дней, отрицательно заархивированные проекты. Промежуточно оцениваются проекты, если от коллабораторов, членов или владельцев проекта есть активность в issues.

По Concise Guide:

- есть значительная активность за последние 12 месяцев
- последняя версия проекта выпущена в течение последних 12 месяцев
- несколько сопровождающих из разных организаций, что позволяет снизить риск возникновения единой точки отказа
- строка с версией не указывает на нестабильность ("0", "alpha", "beta")


Если проект не поддерживается, то он, скорее всего, небезопасен. Для библиотеки запуска процессов регулярно появляются новые векторы атак на уровне ОС, поэтому активное сопровождение критично.

### Критерий 2. Известные уязвимости (Vulnerabilities)

Ссылка: https://github.com/ossf/scorecard/blob/main/docs/checks.md#vulnerabilities

По Scorecard:

Scorecard через сервис OSV (https://osv.dev) проверяет, есть ли у проекта незакрытые уязвимости. Каждая обнаруженная незакрытая уязвимость снижает оценку. Если в проекте не найдено известных уязвимостей, то положительная оценка.

По Concise Guide:

- актуальная версия свободна от известных серьёзных уязвимостей
- проверка информации на https://deps.dev, https://osv.dev

Для CVE существуют готовые эксплойты. Если в проекте есть незакрытые CVE то их можно применить. В случае библиотеки работы с системными процессами -- риск RCE.

### Критерий 3. Обязательный Code Review (Code-Review)

Ссылка: https://github.com/ossf/scorecard/blob/main/docs/checks.md#code-review

По Scorecard:

Оценивается, требуется ли в проекте проверка кода другим разработчиком перед merge в основную ветку. По каким параметрам: попадали ли последние ~30 коммитов на ревью, доля PR, одобренных одним ревьюером, неявные ревью (merge делате не автор коммита). Высокая оценка, если все изменения проходят ревью.

По Concise Guide:

- branch protection на GitHub/GitLab

Ревью может обнаружить и непреднамеренные ошибки, и попытки внедрения вредоносного кода.

### Критерий 4. Политика безопасности (Security-Policy)

Ссылка: https://github.com/ossf/scorecard/blob/main/docs/checks.md#security-policy

По Scorecard:

Проверяется наличие файла SECURITY.md в репозитории проекта с описаниемп процесса раскрытия уязвимостей (responsible disclosure): куда сообщать, сроки ожидания ответа. Выставляется положительная оценка, когда файл есть.

По Concise Guide:

- есть инструкции по сообщению об уязвимостях
- исправляются ли ошибки (особенно связанные с безопасностью) своевременно и выпускаются ли исправления для старых версий

Без инструкции исследователь, обнаруживший проблему, может публично раскрыть её или не сообщить о ней.

### Критерий 5. Статический анализ кода (SAST)

Ссылка: https://github.com/ossf/scorecard/blob/main/docs/checks.md#sast

По Scorecard:

Проверяется, применяются ли инструменты статического анализа SAST (CodeQL, SonarCloud) по их конфигурации в CI-workflow проекта. Если на каждый PR используются инструменты sast в CI то ставится максимальная оценка.

По Concise Guide:

- наличие автоматических тестов в CI-пайплайне

SAST может ообнаруживать уязвимости при каждом коммите. Для библиотеки запуска системных процессов, например, может обнаружиться, что небезопасно формируются строки команд, не экранируются аргументы.

### Критерий 6. Управление зависимостями (Dependency-Update-Tool & Pinned-Dependencies)

Ссылки:
- https://github.com/ossf/scorecard/blob/main/docs/checks.md#dependency-update-tool
- https://github.com/ossf/scorecard/blob/main/docs/checks.md#pinned-dependencies

По Scorecard:

- Dependency-Update-Tool: проверяется, использует ли проект инструменты для обновления зависимостей (Dependabot, Renovate bot). Проверка определяет только, включён ли инструмент, но не гарантирует, что он запущен и PR которые им предлагаются мерджатся.
- Pinned-Dependencies: проверяется, используются ли конкретные версии через хеши, а не теги (latest, main, v1 и тд)

По Concise Guide:

- зависимости пакета актуальны
- библиотека не добавляет ненужных косвенных зависимостей

Даже если библиотека безопасна, её зависимости могут быть уязвимыми, поэтому важно фиксировать версии, минимизировать количество зависимостей.

### Критерий 7. Безопасность API по умолчанию (Secure Defaults & Interface Design)

Ссылки:
- https://best.openssf.org/Concise-Guide-for-Evaluating-Open-Source-Software.html (Usability & Security)
- https://www.bestpractices.dev/en/criteria/0 (know_secure_design)
- https://best.openssf.org/Concise-Guide-for-Developing-More-Secure-Software.html

По Concise Guide:

- проверка, что по умолчанию конфигурация безопасна (Secure Defaults)
- интерфейс/API спроектирован так, чтобы его было легко использовать безопасно (например, поддержка параметризованных запросов) (Interface Design)
- руководство по безопасному использованию библиотеки (Security Guidance)


### Критерий 8. Лицензия и OpenSSF Best Practices Badge (License & CII-Best-Practices)
 
Ссылки:
- https://github.com/ossf/scorecard/blob/main/docs/checks.md#license
- https://github.com/ossf/scorecard/blob/main/docs/checks.md#cii-best-practices
- https://www.bestpractices.dev/en/criteria/0
 
По Scorecard:
 
- Поиск файлов с именами `LICENSE`, `LICENCE`, `COPYING`, `COPYRIGHT` с расширениями `.html`, `.txt`, `.md` в корневой директории или в директории `LICENSES/`. За обнаружение файла начисляется 6/10 баллов, за расположение в корне — ещё 3/10.
- в CII-Best-Practices проверяется, заработал ли проект OpenSSF Best Practices Badge уровня passing, silver или gold.

### Критерий 9. Непрерывное тестирование и фаззинг (CI-Tests & Fuzzing)
 
Ссылки:
- https://github.com/ossf/scorecard/blob/main/docs/checks.md#ci-tests
- https://github.com/ossf/scorecard/blob/main/docs/checks.md#fuzzing
 
По Scorecard:
 
- CI-Tests проверяет, запускаются ли автоматические тесты при каждом PR, каково покрытие тестами
- фаззинг проверяет, использует ли проект фаззинг-инструменты (напр OSS-Fuzz). Принцип работы: автоматически генерируются случайные входные данные с целью поиска crush, утечек памяти, непредвиденного поведения. Фаззинг важен для библиотек, обрабатывающих внешний ввод: он может обнаружить кейс, при котором экранирование ломается и возникает command injection.

---

## 2. Выбор библиотеки

Для варианта php + запуск системных процессов (команды с параметрами от пользователя) возьму библиотеку **symfony/process** https://github.com/symfony/process 

| Критерий | Оценка |
|---|---|
| **1. Maintained** | В репозитории 1970 коммитов, 7.5k star, 108 форков. Первый релиз в октябре 2011 года, последняя версия — 8.0.8 Ежедневно ~715 тыс. скачиваний, общее количество скачиваний — 977 миллионов, 5924 зависимых пакета. Релизы выпускаются по строгому расписанию, есть LTS-версии. Имеет 4108 контрибьюторов кода https://symfony.com/contributors, в версиях нет пометок alpha/beta.|
| **2. Vulnerabilities** | CVE-2026-24739 (некорректное экранирование аргументов в среде MSYS2/Git Bash на Windows, уровень средний, обнаружена в январе 2026): компонент Symfony Process неправильно обрабатывал некоторые символы (=) как «специальные» при экранировании аргументов в Windows. Когда PHP запускался в среде на основе MSYS2 (например, в Git Bash), а Symfony Process запускал собственные исполняемые файлы Windows, преобразование аргументов и путей в MSYS2 могло некорректно обрабатывать аргументы без кавычек, содержащие эти символы. Это теоретически может привести к тому, что порожденный процесс получит поврежденные или усеченные аргументы. Для Linux-дистрибутивов известных уязвимостей нет. Проверила на https://osv.dev  <br><br> <img width="500" height="250" alt="image" src="https://github.com/user-attachments/assets/1aa86981-6d20-45ea-8e0c-6383cbf66e83" /> <img width="500" height="250" alt="image" src="https://github.com/user-attachments/assets/0273c67d-258d-468f-a33f-cb043b752b9b" />|
| **3. Code-Review** | Все изменения проходят через PR в основной монорепозиторий `symfony/symfony`, где требуется review от core team. Branch protection включён. Есть документированный contributing guide с требованиями к PR. |
| **4. Security-Policy** | Уязвимости принимаются по адресу security@symfony.com: процесс включает подтверждение, разработку патча, координацию с downstream-проектами, публикацию advisory и CVE. Symfony зарегистрирован на HackerOne. |
| **5. SAST** | В CI используются PHPUnit, PHP CS Fixer, PHPStan (статический анализ типов). Специализированные SAST-инструменты безопасности (CodeQL, SonarCloud) не интегрированы в публичный CI-workflow. SensioLabs предоставляет коммерческий аудит безопасности. |
| **6. Dependency Management** | Единственное требование — `php: >=8.2`. Нет зависимостей от сторонних пакетов. Монорепозиторий использует `composer.lock` для прописывания зависимостей в CI. |
| **7. Secure Defaults** | Конструктор принимает массив аргументов: `new Process(['ls', '-lsa'])` — аргументы передаются в `proc_open` без вызова shell, угрозы command injection нет. Для случаев, когда нужен shell, существует отдельный метод `Process::fromShellCommandline()` (не default). В документации есть безопасные примеры. |
| **8. Лицензияs** | Лицензия MIT, файл LICENSE присутствует в корне репозитория. |
| **9. CI-Tests & Fuzzing** | Фаззинг-инструменты (OSS-Fuzz и др.) не используются. Тесты (PHPUnit) запускаются в CI на каждый PR в основном репозитории `symfony/symfony`.|

Кроме того, можно зайти на https://deps.dev/project/github/symfony%2Fprocess и посмотреть анализ OpenSSF scorecard:
<img width="300" height="200" alt="image" src="https://github.com/user-attachments/assets/f56acda4-52b1-4137-a95e-d42801974686" />
<img width="300" height="300" alt="image" src="https://github.com/user-attachments/assets/96a56d2c-e4d8-4f61-8426-4fe375d7284e" />
<img width="300" height="150" alt="image" src="https://github.com/user-attachments/assets/92ce0839-61c5-4b8c-9c6e-a2d3c569f11f" />

Общая оценка здесь 4.5 из 10. Но возможно проблема в том, что анализируется symfony/process, а разработка ведется в symfony/symfony.

## 3. Правила безопасного кодирования

Правила должны формализовать защиту от command injection (внедрение команд ос через ввод), контроль ввода пользовательских данных, ограничить поведение процесса, ограничить раскрытие логики через ошибки и тд.

1. Передавать команду и аргументы как массив, а не как строку.

Класс Symfony\Component\Process\Process имеет два способа создания: конструктор new Process(array) (без использования shell) и статический метод Process::fromShellCommandline(string) (передаёт строку в sh -c). В случае использования массива пользовательский ввод не интерпретируется как команда (предотвращение command injection)

```php
use Symfony\Component\Process\Process;
 
$process = Process::fromShellCommandline("grep -r $userInput /data"); // неверно
 
$process = new Process(['grep', '-r', $userInput, '/data']); //верно
```

3. Валидировать пользовательский ввод

Ограничение допустимых символов (конструктор Process(array $command) не валидирует, нужно навешивать отдельно разработчику.

4. Использовать белые списки команд

В конструкторе Process первый элемент -- это имя исполняемого файла, если нет проверки, то пользователь может запустить произвольную команду.

```php
use Symfony\Component\Process\Process;

$process = new Process([$_GET['cmd'], $arg]); // без белых списков
 
$allow = ['ping', 'traceroute', 'nslookup']; //// проверка по белому списку
if (!in_array($cmd, $allow, true)) {exit}
```
   
6. Ограничивать параметры команд

Если пользователь контролирует флаги команд (параметры), то он может влиять на поведение команды. Поэтому можно прописывать белые списки и для параметров команд.

```php
$allowedFlags = ['-l', '-a'];
if (!in_array($flag, $allowedFlags)) exit;
```

8. Ограничивать время выполнения для предотвращения ddos

Через метод Process::setTimeout(?float $timeout) можно задать max время выполнения в секундах (по умолчанию — 60 секунд, значени null снимает ограничение).
```php
use Symfony\Component\Process\Process;

$process->setTimeout(null); // таймаут отключён — процесс может висеть бесконечно

$process->setTimeout(30); //явный таймаут 30 секунд
```

9. Контролировать окружение

Аргумент $env конструктора Process(array $command, ?string $cwd, ?array $env) — массив переменных окружения для подпроцесса. Через пользовательский ввод злоумшленник может подменить значение переменных окружения. Пример:

```php
$process->setEnv(['PATH' => '/usr/bin']);
```

10. Не раскрывать информацию через ошибки пользователю.

Метод Process::getErrorOutput() возвращает содержимое stderr подпроцесса. Метод `Process::isSuccessful()` проверяет код завершения (0 = успех). Вместо вывода getErrorOutput() можно кидать исключение, прерывающее выполнение команды. Например:

```php
use Symfony\Component\Process\Process;

echo $process->getErrorOutput(); // небезопасно: вывод stderr пользователю 
 
if (!$process->isSuccessful()) { //безопасно: в лог выводится stderr, пользователю сообщение об ошибке в общем виде
    error_log($process->getErrorOutput());
    throw new Exception("Command failed");
}
```

## 4. Semgrep правила, тесты


