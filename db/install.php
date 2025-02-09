<?php
// This script runs when the plugin is installed.

defined('MOODLE_INTERNAL') || die();

function xmldb_customcert_install() {
    global $DB;

    $dbman = $DB->get_manager();

    // Define the table structure.
    $table = new xmldb_table('customcert_sequential_codes');

    // Add fields.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('certificateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('value', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    // Add primary key.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Check if the table exists before creating it.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}
