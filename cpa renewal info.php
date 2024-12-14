<?php
/**
 * Plugin Name: CPA Renewal Info (Full State Names)
 * Plugin URI:  https://example.com/
 * Description: Displays CPA license renewal requirements by full state name. Approximate data included. Always verify with your State Board.
 * Version:     1.6.0
 * Author:      Your Name
 * Author URI:  https://example.com/
 * License:     GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class CPA_Renewal_Info_Plugin_Full_Names {
    private $option_name = 'cpa_renewal_info_data_full_names';

    public function __construct() {
        // Force re-populate data on activation
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'settings_init' ) );
        add_shortcode( 'cpa_info', array( $this, 'display_cpa_info_shortcode' ) );
    }

    public function activate() {
        // Approximate/generic patterns
        $biennial_generic = array(
            'credits_needed' => '80',
            'timeframe' => 'Every 2 years',
            'ethics_credits_needed' => '4',
            'yearly_credits' => 'N/A',
            'renewal_month' => 'Varies by licensee',
            'reporting_period' => 'Biennial period (check state board)',
            'additional_requirements' => 'Check with your State Board for specifics.'
        );

        $annual_generic = array(
            'credits_needed' => '40',
            'timeframe' => 'Every year',
            'ethics_credits_needed' => '2',
            'yearly_credits' => '40',
            'renewal_month' => 'Varies by licensee',
            'reporting_period' => 'Annual reporting',
            'additional_requirements' => 'Check with your State Board for specifics.'
        );

        $triennial_generic = array(
            'credits_needed' => '120',
            'timeframe' => 'Every 3 years',
            'ethics_credits_needed' => '4',
            'yearly_credits' => 'N/A',
            'renewal_month' => 'Varies by licensee',
            'reporting_period' => 'Triennial period (check state board)',
            'additional_requirements' => 'Check with your State Board for specifics.'
        );

        // Some states with approximate specific data:
        $california = array(
            'credits_needed' => '80',
            'timeframe' => 'Every 2 years',
            'ethics_credits_needed' => '4',
            'yearly_credits' => 'N/A (20 hours/year recommended)',
            'renewal_month' => 'Last day of birth month (biennial)',
            'reporting_period' => 'Biennial period ending birth month',
            'additional_requirements' => '2-hour Regulatory Review course each renewal.'
        );

        $new_york = array(
            'credits_needed' => '24 annually (or 40 with other track)',
            'timeframe' => '3-year license, annual CPE requirement',
            'ethics_credits_needed' => '4 every 3-year cycle',
            'yearly_credits' => '24 or 40 depending on track',
            'renewal_month' => 'Tied to birth month on a 3-year cycle',
            'reporting_period' => 'Triennial, but must meet annual CPE',
            'additional_requirements' => 'Tax concentration options; verify with NYSED.'
        );

        $texas = array(
            'credits_needed' => '120 over 3-year rolling period',
            'timeframe' => '3-year rolling period',
            'ethics_credits_needed' => '4 hours TX-approved ethics every 2 years',
            'yearly_credits' => 'At least 20 per year',
            'renewal_month' => 'Often tied to birth month',
            'reporting_period' => '3-year rolling period',
            'additional_requirements' => 'Must take a Board-approved ethics course.'
        );

        $illinois = array(
            'credits_needed' => '120',
            'timeframe' => 'Every 3 years',
            'ethics_credits_needed' => '4 every 3-year period',
            'yearly_credits' => 'N/A',
            'renewal_month' => 'November 30 (triennial)',
            'reporting_period' => 'Triennial ending Nov 30',
            'additional_requirements' => '1 hour Sexual Harassment Prevention Training.'
        );

        $florida = array(
            'credits_needed' => '80',
            'timeframe' => 'Every 2 years',
            'ethics_credits_needed' => '4 (FL Board-approved)',
            'yearly_credits' => 'N/A',
            'renewal_month' => 'December 31 (odd years)',
            'reporting_period' => 'Biennial ending Dec 31 odd years',
            'additional_requirements' => '8 hours A&A subjects each cycle.'
        );

        // Create a large array of states with full names:
        $states_data = array(
            'Alabama' => $annual_generic,
            'Alaska' => $biennial_generic,
            'Arizona' => $biennial_generic,
            'Arkansas' => $annual_generic,
            'California' => $california,
            'Colorado' => $biennial_generic,
            'Connecticut' => $biennial_generic,
            'Delaware' => $biennial_generic,
            'Florida' => $florida,
            'Georgia' => $biennial_generic,
            'Hawaii' => $biennial_generic,
            'Idaho' => $biennial_generic,
            'Illinois' => $illinois,
            'Indiana' => $triennial_generic,
            'Iowa' => $triennial_generic,
            'Kansas' => $triennial_generic,
            'Kentucky' => $triennial_generic,
            'Louisiana' => $triennial_generic,
            'Maine' => $annual_generic,
            'Maryland' => $biennial_generic,
            'Massachusetts' => $biennial_generic,
            'Michigan' => $biennial_generic,
            'Minnesota' => $triennial_generic,
            'Mississippi' => $annual_generic,
            'Missouri' => $triennial_generic,
            'Montana' => $triennial_generic,
            'Nebraska' => $biennial_generic,
            'Nevada' => $biennial_generic,
            'New Hampshire' => $triennial_generic,
            'New Jersey' => $triennial_generic,
            'New Mexico' => $triennial_generic,
            'New York' => $new_york,
            'North Carolina' => $annual_generic,
            'North Dakota' => $triennial_generic,
            'Ohio' => $triennial_generic,
            'Oklahoma' => $triennial_generic,
            'Oregon' => $biennial_generic,
            'Pennsylvania' => $biennial_generic,
            'Rhode Island' => $triennial_generic,
            'South Carolina' => $biennial_generic,
            'South Dakota' => $triennial_generic,
            'Tennessee' => $biennial_generic,
            'Texas' => $texas,
            'Utah' => $biennial_generic,
            'Vermont' => $biennial_generic,
            'Virginia' => $triennial_generic,
            'Washington' => $triennial_generic,
            'West Virginia' => $triennial_generic,
            'Wisconsin' => $biennial_generic,
            'Wyoming' => $triennial_generic,
        );

        // Force update option to ensure fresh data
        update_option( $this->option_name, $states_data );
    }

    public function add_admin_menu() {
        add_options_page(
            'CPA Renewal Info',
            'CPA Renewal Info',
            'manage_options',
            'cpa_renewal_info',
            array( $this, 'options_page' )
        );
    }

    public function settings_init() {
        register_setting( 'cpaRenewalInfoGroup', $this->option_name, array( 'sanitize_callback' => array($this, 'sanitize_data') ) );

        add_settings_section(
            'cpa_renewal_section',
            'CPA Renewal Requirements',
            array( $this, 'settings_section_callback' ),
            'cpaRenewalInfoGroup'
        );
    }

    public function settings_section_callback() {
        echo '<p>Select a state and update the CPE requirements. (Approximate data - verify with official sources.)</p>';
    }

    public function sanitize_data($input) {
        if (!is_array($input)) return array();
        $clean = array();
        foreach($input as $state => $data) {
            $clean[$state] = array(
                'credits_needed' => sanitize_text_field($data['credits_needed']),
                'timeframe' => sanitize_text_field($data['timeframe']),
                'ethics_credits_needed' => sanitize_text_field($data['ethics_credits_needed']),
                'yearly_credits' => sanitize_text_field($data['yearly_credits']),
                'renewal_month' => sanitize_text_field($data['renewal_month']),
                'reporting_period' => sanitize_text_field($data['reporting_period']),
                'additional_requirements' => sanitize_text_field($data['additional_requirements']),
            );
        }
        return $clean;
    }

    public function options_page() {
        $states_data = get_option( $this->option_name, array() );
        $all_states = array_keys($states_data);
        sort($all_states, SORT_STRING);

        $selected_state = isset($_GET['selected_state']) ? sanitize_text_field($_GET['selected_state']) : reset($all_states);

        if (!isset($states_data[$selected_state])) {
            $states_data[$selected_state] = array(
                'credits_needed' => '',
                'timeframe' => '',
                'ethics_credits_needed' => '',
                'yearly_credits' => '',
                'renewal_month' => '',
                'reporting_period' => '',
                'additional_requirements' => '',
            );
        }

        if ( isset($_POST['submit']) && check_admin_referer('cpa_renewal_info_save', 'cpa_renewal_info_nonce') ) {
            $states_data[$selected_state]['credits_needed'] = sanitize_text_field($_POST['credits_needed']);
            $states_data[$selected_state]['timeframe'] = sanitize_text_field($_POST['timeframe']);
            $states_data[$selected_state]['ethics_credits_needed'] = sanitize_text_field($_POST['ethics_credits_needed']);
            $states_data[$selected_state]['yearly_credits'] = sanitize_text_field($_POST['yearly_credits']);
            $states_data[$selected_state]['renewal_month'] = sanitize_text_field($_POST['renewal_month']);
            $states_data[$selected_state]['reporting_period'] = sanitize_text_field($_POST['reporting_period']);
            $states_data[$selected_state]['additional_requirements'] = sanitize_text_field($_POST['additional_requirements']);

            update_option($this->option_name, $states_data);
            echo '<div class="updated"><p>Information updated for ' . esc_html($selected_state) . '</p></div>';
        }

        ?>
        <div class="wrap">
            <h1>CPA Renewal Info</h1>

            <form method="get" action="">
                <input type="hidden" name="page" value="cpa_renewal_info" />
                <select name="selected_state">
                    <?php foreach($all_states as $state): ?>
                        <option value="<?php echo esc_attr($state); ?>" <?php selected($state, $selected_state); ?>>
                            <?php echo esc_html($state); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" class="button button-secondary" value="Select State" />
            </form>

            <hr />

            <form method="post" action="">
                <?php wp_nonce_field('cpa_renewal_info_save', 'cpa_renewal_info_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">State</th>
                        <td><strong><?php echo esc_html($selected_state); ?></strong></td>
                    </tr>
                    <tr>
                        <th scope="row">Total Credits Needed</th>
                        <td><input type="text" name="credits_needed" value="<?php echo esc_attr($states_data[$selected_state]['credits_needed']); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Timeframe</th>
                        <td><input type="text" name="timeframe" value="<?php echo esc_attr($states_data[$selected_state]['timeframe']); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Ethics Credits Needed</th>
                        <td><input type="text" name="ethics_credits_needed" value="<?php echo esc_attr($states_data[$selected_state]['ethics_credits_needed']); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Yearly Credits</th>
                        <td><input type="text" name="yearly_credits" value="<?php echo esc_attr($states_data[$selected_state]['yearly_credits']); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Renewal Month</th>
                        <td><input type="text" name="renewal_month" value="<?php echo esc_attr($states_data[$selected_state]['renewal_month']); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Reporting Period</th>
                        <td><input type="text" name="reporting_period" value="<?php echo esc_attr($states_data[$selected_state]['reporting_period']); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Additional Requirements</th>
                        <td>
                            <textarea name="additional_requirements" rows="3" cols="50">
                                <?php echo esc_textarea($states_data[$selected_state]['additional_requirements']); ?>
                            </textarea>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save Changes'); ?>
            </form>
        </div>
        <?php
    }

    public function display_cpa_info_shortcode($atts) {
        $states_data = get_option($this->option_name, array());
        $all_states = array_keys($states_data);
        sort($all_states, SORT_STRING);

        $data_json = json_encode($states_data);
        ob_start();
        ?>
        <div class="cpa-info-container">
            <h3>CPA Renewal Requirements</h3>
            <p>Select a state to view the requirements (Approximate data - verify with official state board):</p>
            <select id="cpa-info-state-selector">
                <option value="">--Select a State--</option>
                <?php foreach($all_states as $state): ?>
                    <option value="<?php echo esc_attr($state); ?>"><?php echo esc_html($state); ?></option>
                <?php endforeach; ?>
            </select>

            <div id="cpa-info-display" style="margin-top:20px; display:none;">
                <h4 id="cpa-info-state"></h4>
                <p><strong>Total Credits Needed:</strong> <span id="cpa-info-credits"></span></p>
                <p><strong>Timeframe:</strong> <span id="cpa-info-timeframe"></span></p>
                <p><strong>Ethics Credits Needed:</strong> <span id="cpa-info-ethics"></span></p>
                <p><strong>Yearly Credits:</strong> <span id="cpa-info-yearly"></span></p>
                <p><strong>Renewal Month:</strong> <span id="cpa-info-renewal"></span></p>
                <p><strong>Reporting Period:</strong> <span id="cpa-info-reporting"></span></p>
                <p><strong>Additional Requirements:</strong> <span id="cpa-info-additional"></span></p>
            </div>
        </div>
        <script type="text/javascript">
        (function() {
            var data = <?php echo $data_json; ?>;
            console.log('CPA Info Data:', data);

            var selector = document.getElementById('cpa-info-state-selector');
            var display = document.getElementById('cpa-info-display');

            var stateEl = document.getElementById('cpa-info-state');
            var creditsEl = document.getElementById('cpa-info-credits');
            var timeframeEl = document.getElementById('cpa-info-timeframe');
            var ethicsEl = document.getElementById('cpa-info-ethics');
            var yearlyEl = document.getElementById('cpa-info-yearly');
            var renewalEl = document.getElementById('cpa-info-renewal');
            var reportingEl = document.getElementById('cpa-info-reporting');
            var additionalEl = document.getElementById('cpa-info-additional');

            selector.addEventListener('change', function() {
                var selected = this.value;
                if (selected && data[selected]) {
                    stateEl.textContent = selected;
                    creditsEl.textContent = data[selected].credits_needed || '';
                    timeframeEl.textContent = data[selected].timeframe || '';
                    ethicsEl.textContent = data[selected].ethics_credits_needed || '';
                    yearlyEl.textContent = data[selected].yearly_credits || '';
                    renewalEl.textContent = data[selected].renewal_month || '';
                    reportingEl.textContent = data[selected].reporting_period || '';
                    additionalEl.textContent = data[selected].additional_requirements || '';
                    display.style.display = 'block';
                } else {
                    display.style.display = 'none';
                }
            });
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}

new CPA_Renewal_Info_Plugin_Full_Names();
