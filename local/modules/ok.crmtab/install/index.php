<?php

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\SystemException;
use Bitrix\Main\IO\InvalidPathException;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\LoaderException;

Loc::getMessage(__FILE__);

class ok_crmtab extends CModule
{
    public $MODULE_ID = 'ok.crmtab';
    public $MODULE_SORT = 500;
    public $MODULE_VERSION;
    public $MODULE_DESCRIPTION;
    public $MODULE_VERSION_DATE;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_DESCRIPTION = Loc::getMessage('OK_CRMTAB_INSTALL_MODULE_DESCRIPTION');
        $this->MODULE_NAME = Loc::getMessage('OK_CRMTAB_INSTALL_MODULE_NAME');
        $this->PARTNER_NAME = Loc::getMessage('OK_CRMTAB_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('OK_CRMTAB_PARTNER_URI');
    }

    /**
     * @throws SystemException
     */
    public function DoInstall(): void
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallFiles();
        $this->InstallDB();
        $this->InstallEvents();
    }

    /**
     * @throws SqlQueryException
     * @throws LoaderException
     * @throws InvalidPathException
     */
    public function DoUninstall(): void
    {
        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();

        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @throws InvalidPathException
     */
    public function InstallFiles($params = []): void
    {
        $component_path = $this->getPath() . '/install/components';

        if (Directory::isDirectoryExists($component_path)) {
            CopyDirFiles($component_path, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components', true, true);
        } else {
            throw new InvalidPathException($component_path);
        }
    }

    public function InstallDB(): void
    {
        Loader::includeModule($this->MODULE_ID);

        $this->installDoctorTable();
        $this->installPatientTable();
        $this->installManyToManyTable();
        $this->installDemoData();
    }

    public function InstallEvents(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\\Ok\\Crmtab\\Handlers',
            'updateTabs'
        );
    }

    public function UnInstallEvents(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\\Ok\\Crmtab\\Handlers',
            'updateTabs'
        );
    }

    /**
     * @throws SqlQueryException
     * @throws LoaderException
     */
    public function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $connection = \Bitrix\Main\Application::getConnection();

        $this->unInstallManyToManyTable();
        $this->unInstallDoctorTable();
        $this->unInstallPatientTable();
    }

    /**
     * Удаляет файлы, установленные компонентом
     * @throws InvalidPathException
     */
    public function UninstallFiles(): void
    {
        $component_path = $this->getPath() . '/install/components';

        if (Directory::isDirectoryExists($component_path)) {
            $installed_components = new \DirectoryIterator($component_path);
            foreach ($installed_components as $component) {
                if ($component->isDir() && !$component->isDot()) {
                    $target_path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/' . $component->getFilename();
                    if (Directory::isDirectoryExists($target_path)) {
                        Directory::deleteDirectory($target_path);
                    }
                }
            }
        } else {
            throw new InvalidPathException($component_path);
        }
    }

    private function installDoctorTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'k_custom_doctor';

        if (!$connection->isTableExists($tableName)) {
            $connection->queryExecute("
            CREATE TABLE {$tableName}(
                ID INT AUTO_INCREMENT PRIMARY KEY,
                DOCTOR VARCHAR(255) NOT NULL
            )
        ");
        }
    }
    private function installPatientTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'k_custom_patient';

        if (!$connection->isTableExists($tableName)) {
            $connection->queryExecute("
            CREATE TABLE {$tableName}(
                ID INT AUTO_INCREMENT PRIMARY KEY,
                PATIENT VARCHAR(255) NOT NULL
            )
        ");
        }
    }
    private function installManyToManyTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'k_custom_service';

        if (!$connection->isTableExists($tableName)) {
            $sql = "
            CREATE TABLE {$tableName} (
                ID INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                DOCTOR_ID INT NOT NULL,
                PATIENT_ID INT NOT NULL,
                DESCRIPTION TEXT,
                CONSTRAINT fk_kcustomservice_doctor_id FOREIGN KEY (DOCTOR_ID) REFERENCES k_custom_doctor(ID) ON DELETE CASCADE,
                CONSTRAINT fk_kcustomservice_patient_id FOREIGN KEY (PATIENT_ID) REFERENCES k_custom_patient(ID) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ";
            $result = $connection->queryExecute($sql);
            if ($result === false) {
                $error = $connection->getErrorMessage();
                file_put_contents($_SERVER['DOCUMENT_ROOT'].'/_LOG/error.log', "Ошибка создания таблицы {$tableName}: {$error}\n", FILE_APPEND);
            }
        }
    }

    /**
     * @throws SqlQueryException
     */
    private function unInstallDoctorTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'k_custom_doctor';

        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }
    }

    private function unInstallPatientTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'k_custom_patient';

        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }
    }

    private function unInstallManyToManyTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'k_custom_service';

        if ($connection->isTableExists($tableName)) {
            $connection->queryExecute("ALTER TABLE `k_custom_service` DROP FOREIGN KEY `fk_kcustomservice_doctor_id`");
            $connection->queryExecute("ALTER TABLE `k_custom_service` DROP FOREIGN KEY `fk_kcustomservice_patient_id`");

            $connection->dropTable($tableName);
        }
    }

    public function getPath($notDocumentRoot = false): string
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    function installDemoData(): void
    {
        $connection = Application::getConnection();
        $tableDoctor = 'k_custom_doctor';
        $tablePatient = 'k_custom_patient';
        $tableService = 'k_custom_service';

        if ($connection->isTableExists($tableDoctor)) {
            $connection->queryExecute("
                INSERT INTO k_custom_doctor (DOCTOR) VALUES 
                    ('Иванов Иван'), 
                    ('Смирнов Сергей'), 
                    ('Петров Алексей'), 
                    ('Фёдоров Николай'), 
                    ('Морозова Елена'),
                    ('Григорьев Артём'),
                    ('Алексеева Наталья');
            ");
        }

        if ($connection->isTableExists($tablePatient)) {
            $connection->queryExecute("
                INSERT INTO k_custom_patient (PATIENT) VALUES ('Кузнецова Анна'), 
                    ('Васильев Олег'), 
                    ('Сидоров Дмитрий'), 
                    ('Борисова Татьяна'), 
                    ('Игнатов Павел'),
                    ('Шестакова Мария'),
                    ('Денисов Илья');
            ");
        }

        if ($connection->isTableExists($tableService)) {
            $connection->queryExecute("
                INSERT INTO k_custom_service (DOCTOR_ID, PATIENT_ID, DESCRIPTION) VALUES
                    (1, 1, 'Первичный приём, консультация по общему самочувствию и жалобам на давление.'),
                    (1, 2, 'Повторный осмотр, коррекция схемы лечения гипертонии.'),
                    (2, 3, 'Стоматологическая чистка зубов и рекомендации по гигиене.'),
                    (2, 4, 'Удаление зуба мудрости, назначен курс обезболивающих.'),
                    (3, 5, 'Общий анализ крови, консультация по результатам анализов.'),
                    (3, 2, 'Диагностика и назначение лечения при ОРВИ, выписан больничный.'),
                    (4, 1, 'Плановый осмотр после операции, швы сняты, состояние удовлетворительное.'),
                    (4, 3, 'Физиотерапевтическая процедура: лазеротерапия при артрозе.'),
                    (5, 4, 'Консультация невролога, жалобы на мигрень, назначено МРТ.'),
                    (5, 5, 'Психологическая консультация, определена причина тревожности.'),
                    (1, 3, 'Контрольное измерение давления, стабильные показатели.'),
                    (2, 1, 'Пломбирование зуба, пациент доволен результатом.'),
                    (3, 4, 'Назначение курса витаминов, общая слабость.'),
                    (4, 2, 'Проведён массаж шейного отдела, снято напряжение.'),
                    (5, 1, 'Психотерапевтическая беседа, выдана рекомендация по сну.');
            ");
        }
    }
}
