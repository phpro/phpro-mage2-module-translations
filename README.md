![](https://github.com/phpro/phpro-mage2-module-translations/workflows/.github/workflows/grumphp.yml/badge.svg)

![](https://shields.io/badge/Hyv%C3%A4_Theme-Compatible---?style=for-the-badge&labelColor=F6F7FF&color=0B23B9&logoColor=0B23B9&logo=data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTkuNjI1IDkuNzdjMS4zNTUtMS4wNDcgMy4wMDgtMS43MDcgNC44NjctMS43MDcgMy44MDUgMCA3LjUwOCAyLjM2NyA3LjUwOCA4LjQzM1YyNGgtNC44Njd2LTcuNTA0YzAtMy4xMjktMS41MjgtNC4zMDgtMy42MTMtNC4zMDgtMS43MjMgMC0yLjk3My45Ni0zLjY2OCAxLjk3NlYyNEg0Ljk2NVY0LjUzMUgyTDMuNTYzIDBoNi4wNjJ2OS43N3pNMTYuNjQgMEgyMmwtMS41NyA0LjUzMWgtNS4zNTJMMTYuNjQgMHoiLz48L3N2Zz4K)

# Translation module for Magento 2

The `Phpro_Translations` module helps you to manage translations via the Magento backend.

## Features
* Simple backend CRUD to manage translations
* Single and multi-inline editing via the grid-overview
* Import and export via CSV files via CLI 
* Import and export via default Magento import/export functionality
* Prepare new translations via data patch scripts
* (Re)generate frontend translations (JSON translation files) via CLI and backend

## Installation
```
composer require phpro/mage2-module-translations
```

## End user documentation
[Download the end user documentation (PDF)](./resources/phpro-translation-module-EUD.pdf)

## Usage (technical)

### Locales 
All locales must be defined in an ISO format. Locale = ISO-639 (language)  + "_" + ISO-3166 (country). 

Examples of locales: en_US, nl_BE, nl_NL, fr_BE, de_DE, ...

### Import and export

#### Import Magento translation CSVs

CSV structure must be (key, value):
```
"Transkey 1","Transvalue 2"
"Transkey 2","Transvalue 2"
...
```
Use the `phpro:translations:import` command to import a CSV file for a given locale. Duplicate records are skipped and no updates are applied. 
```
bin/magento phpro:translations:import /path/to/cs_CZ/cs_CZ.csv cs_CZ --clear-cache
```
Output:
```
Importing CSV file /path/to/cs_CZ/cs_CZ.csv for locale cs_CZ
#created: 193
#skipped: 4
#failed: 0
Caches cleared: full_page, block_html, translate
Done!
```

#### Export to CSV

Use the `phpro:translations:export` command to export database translations to a CSV file for one or more locales. Separate multiple locales with a space. The exported CSV file is written in the `var/translations` folder of Magento. 
```
bin/magento phpro:translations:export nl_BE cs_CZ
```
Output:
```
Exporting translations to CSV file
Csv file: /path/to/var/translations/20190531_085402_export_nl_BE_cs_CZ.csv
Total written rows: 390
Done!
```
CSV structure:
```
"New Account","Nieuwe account",nl_BE
"My Wish List","Mijn verlanglijst",nl_BE
"New Account","Nový účet",cs_CZ
"My Wish List","Mé oblíbené",cs_CZ
...
```

#### Import CSV (including locale)
You can use the exported CSV file(s) to import on another environment. For example you can prepare new translations on a staging environment and import them later on a production environment. 

CSV structure must be (key, value, locale):
```
"Transkey 1","Transvalue 2",nl_BE
"Transkey 2","Transvalue 2",nl_BE
"Transkey 1","Transvalue 2",fr_BE
"Transkey 2","Transvalue 2",fr_BE
...
```
Use the `phpro:translations:import-full` command to import a CSV file. Duplicate records are skipped and no updates are applied. 
```
bin/magento phpro:translations:import-full /path/to/full_import_nl_BE_cs_CZ.csv --clear-cache
```
Output:
```
Importing CSV file /path/to/full_import_nl_BE_cs_CZ.csv
#created: 10
#skipped: 380
#failed: 0
Caches cleared: full_page, block_html, translate
Done!
```

#### Import via backend
Go to System → (Data Transfer) → Import to create or update translations based on a CSV file. Please reach out our end user documentation. 

### Re-generate frontend translations
Generating/re-generating frontend translation will generate JSON file(s) which includes all the frontend/JS translations.
These files are stored in the `pub/media/phpro_translations` directory in their related theme/locale subdirectory.

#### Via backend
- Go to "Translations -> Generate frontend translations" in the admin. Select "all store views" or select specific ones. Click the button "Generate translations files".
- Clear full page and block html caches via "System -> Cache Management".
- Also check the end user documentation

#### Via CLI
- Use the `phpro:translations:generate-frontend-translations` command to re-generate new JSON file(s) 
- Make sure you clean full_page and block_html cache manually afterwards to apply and enable the newest translation JSON file for the storefront.

**Re-generate for single store view**

Specify the `storeId` argument to re-generate for specific store view (locale). Use the `bin/magento store:list` command to show the store IDs.  
```
bin/magento phpro:translations:generate-frontend-translations 5
```

**Re-generate for all**

Leave the `storeId` argument empty to re-generate for all store views.
```
bin/magento phpro:translations:generate-frontend-translations
```

#### During build process
We recommend to re-generate all frontend translations during your build process with `phpro:translations:generate-frontend-translations` after the `setup:upgrade --keep-generated` step and just before deactivating maintenance.

#### Browser cache optimizations
The translations JSON files are stored in the directory `pub/media/phpro_translations` with a specific version string. 
You can choose to have a these files optimally cached by browsers by configuring the Cache-Control header. A ngnix example below: 
```
location /media/phpro_translations/ {
    add_header X-Frame-Options "SAMEORIGIN";
    add_header Cache-Control "public";
    expires +1y;
}
```

### Collect translations from code base
Use the `phpro:translations:prepare-keys` command to collect translations phrases from the code base and prepare them. This will create a translation for every available locale.

### Add translations during development
To prepare, create or delete translations you can inject `\Phpro\Translations\Api\TranslationDataManagementInterface` as dependency into your data patch script of your module. 

#### Prepare
Add the translation key for all enabled locales of the Magento instance. If default translation is not set, the translation key will be used as default translation.

```
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;
use Phpro\Translations\Model\TranslationDataManagement;

class HelloWorldTranslations implements DataPatchInterface, NonTransactionableInterface
{
    private TranslationDataManagement $translationDataManagement;
    
    public function __construct(
        TranslationDataManagement $translationDataManagement
    ) {
        $this->translationDataManagement = $translationDataManagement;
    }

    public function apply() 
    {
        $this->translationDataManagement->prepare('Hello world!');
        $this->translationDataManagement->prepare('Welcome %1', 'Hello %1');
        // other translation keys here...
    }
}    
```
#### Create
Add a translation for given locales.
```
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;
use Phpro\Translations\Model\TranslationDataManagement;

class HelloWorldTranslations implements DataPatchInterface, NonTransactionableInterface
{
    private TranslationDataManagement $translationDataManagement;

    public function __construct(
        TranslationDataManagement $translationDataManagement
    ) {
        $this->translationDataManagement = $translationDataManagement;
    }

    public function apply() 
    {
        $this->translationDataManagement->create('Hello world!', 'Hallo wereld!!!', ['nl_NL', 'nl_BE']);
        $this->translationDataManagement->create('Hello world!', 'Hello world!!!', ['en_US']);
        // other translation keys here...
    }
}
```
#### Delete
Delete a translation for given translation key and locale(s). In case no locales are given, all enabled locales will be used for deletion.
```
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;
use Phpro\Translations\Model\TranslationDataManagement;

class DeleteHelloWorldTranslations implements DataPatchInterface, NonTransactionableInterface
{
    private TranslationDataManagement $translationDataManagement;

    public function __construct(
        TranslationDataManagement $translationDataManagement
    ) {
        $this->translationDataManagement = $translationDataManagement;
    }

    public function apply() 
    {
        $this->translationDataManagement->delete('Hello world!');
        $this->translationDataManagement->delete('Welcome %1', ['nl_BE', 'nl_NL']);
        // other translation keys here...
    }
}
```
## PWA
The checkbox "frontend" could be used to mark translations that need to be exported to a PWA installation.
With a rest API call, the translations can be fetched and stored in the PWA translations files.

### API call that can be used in build scripts
```
curl -G -k -H "Authorization: Bearer <token>" --data-urlencode "searchCriteria[filter_groups][0][filters][0][field]=frontend" --data-urlencode "searchCriteria[filter_groups][0][filters][0][value]=1" --data-urlencode "searchCriteria[filter_groups][1][filters][0][field]=locale" --data-urlencode "searchCriteria[filter_groups][1][filters][0][value]=<locale>" <Magento base url>rest/V1/phpro-translations/translation/search
```

## Hyvä

Install the Hyvä compatibilty module. Minimum version is 1.2.3.
```
composer require hyva-themes/magento2-phpro-translations
```