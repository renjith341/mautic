<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\Migrations;

use Doctrine\DBAL\Migrations\SkipMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Mautic\CoreBundle\Doctrine\AbstractMauticMigration;

/**
 * Class Version20171124065133.
 */
class Version20171124065133 extends AbstractMauticMigration
{
    /**
     * @param Schema $schema
     *
     * @throws SkipMigrationException
     */
    public function preUp(Schema $schema)
    {
        if ($schema->hasTable(MAUTIC_TABLE_PREFIX.'companies_assets_xref')) {
            throw new SkipMigrationException('Schema includes this migration');
        }
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("CREATE TABLE  {$this->prefix}companies_assets_xref (company_id INT NOT NULL, asset_id INT NOT NULL, INDEX ".$this->generatePropertyName('companies_assets_xref', 'idx', ['company_id']).' (company_id), INDEX '.$this->generatePropertyName('companies_assets_xref', 'idx', ['asset_id']).' (asset_id), PRIMARY KEY(company_id, asset_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql("ALTER TABLE  {$this->prefix}companies_assets_xref ADD CONSTRAINT ".$this->generatePropertyName('companies_assets_xref', 'fk', ['company_id']).' FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE');
        $this->addSql("ALTER TABLE  {$this->prefix}companies_assets_xref ADD CONSTRAINT ".$this->generatePropertyName('companies_assets_xref', 'fk', ['asset_id']).' FOREIGN KEY (asset_id) REFERENCES assets (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("DROP TABLE  {$this->prefix}companies_assets_xref");
    }
}
