<?php

namespace Drupal\nisum_migration\Drush\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Utility\Token;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Drush\Attributes as CLI;
use Drush\Commands\AutowireTrait;
use Drush\Commands\DrushCommands;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateManagerInterface;

/**
 * A Drush commandfile.
 */
final class NisumMigrationCommands extends DrushCommands {

  use AutowireTrait;

  /**
   * Constructs a NisumMigrationCommands object.
   */
  public function __construct(
    private readonly Token $token,
    MigrationPluginManagerInterface $migrationPluginManager,
  ) {
    parent::__construct();
    $this->migrateManager = $migrationPluginManager;
  }

  /**
     * Triggers the migration process.
     *
     * @command nisum-migration:run
     * @aliases nmigrate
     * @usage nisum-migration:run
     *   Runs the migrations for companies and users.
     */
    public function runMigrations() {
      $migrations = ['migrate_companies', 'migrate_users'];

      foreach ($migrations as $migration_id) {
          try {
              $migration = $this->migrateManager->createInstance($migration_id);
              $executable = new MigrateExecutable($migration);
              $executable->import();
              $this->output()->writeln(dt('Migration @migration completed successfully.', ['@migration' => $migration_id]));
          } catch (MigrateException $e) {
              $this->output()->writeln(dt('Migration @migration failed: @message', [
                  '@migration' => $migration_id,
                  '@message' => $e->getMessage(),
              ]));
          }
      }
  }

}
