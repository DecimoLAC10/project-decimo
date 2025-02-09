<?php

namespace customcertelement_sequentialcode;

defined('MOODLE_INTERNAL') || die();

class element extends \mod_customcert\element {

    /**
     * Get the name of the element.
     */
    public function get_name() {
        return get_string('elementname', 'customcertelement_sequentialcode');
    }

    /**
     * This function defines form elements when adding/editing this element.
     *
     * @param \MoodleQuickForm $mform The edit_form instance.
     */
    public function render_form_elements($mform) {
        $mform->addElement('text', 'defaultvalue', get_string('defaultvalue', 'customcertelement_sequentialcode'));
        $mform->setType('defaultvalue', PARAM_TEXT);
        parent::render_form_elements($mform);
    }

    /**
     * Save unique data when form is submitted.
     *
     * @param \stdClass $data The form data.
     * @return string
     */
    public function save_unique_data($data) {
        return $data->defaultvalue;
    }

    /**
     * Generate the sequential code and render it.
     */
    public function render($pdf, $preview, $user) {
        global $DB;

        try {
            // Ensure table exists before querying.
            if (!$DB->get_manager()->table_exists('customcert_sequential_codes')) {
                debugging('Table customcert_sequential_codes does not exist.', DEBUG_DEVELOPER);
                return;
            }

            // Fetch the last used code (prevent NULL issues).
            $lastcode = $DB->get_field_sql("SELECT MAX(value) FROM {customcert_sequential_codes}");
            $newcode = isset($lastcode) ? $lastcode + 1 : 100000; // Start from 100000 if no codes exist.

            // Save new code in a custom table.
            $record = new \stdClass();
            $record->certificateid = $this->get_certificateid();
            $record->value = $newcode;
            $record->timecreated = time();
            $DB->insert_record('customcert_sequential_codes', $record);

            // Render the code on the certificate.
            \mod_customcert\element_helper::render_content($pdf, $this, $newcode);
        } catch (\Exception $e) {
            debugging('Database error in sequentialcode element: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Render the element in HTML for the certificate editor.
     *
     * @return string
     */
    public function render_html() {
        return \mod_customcert\element_helper::render_html_content($this, 'Sequential Code');
    }

    /**
     * Helper function to get the certificate ID safely.
     */
    private function get_certificateid() {
        return isset($this->element->certificateid) ? $this->element->certificateid : 0;
    }
}
