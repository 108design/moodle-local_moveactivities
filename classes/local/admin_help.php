<?php
// Copyright (C) 2026 Andreas Giesen. See LICENSE.md.
namespace local_moveactivities\local;

defined('MOODLE_INTERNAL') || die();

/** Contextual settings help, following the standalone Frontpage panel contract. */
final class admin_help {
    private const TOPICS = ['overview', 'results'];
    private static bool $prepared = false;

    /** Register only the permitted settings page before its header is rendered. */
    public static function prepare(\core\hook\output\before_http_headers $hook): void {
        global $CFG, $PAGE;
        if (self::$prepared || admin_branding::page_capability() === null) {
            return;
        }
        $PAGE->add_body_class('local-moveactivities-admin-help-page');
        self::$prepared = true;
        $PAGE->requires->css(new \moodle_url('/local/moveactivities/styles/admin_help.css', [
            'rev' => get_config('local_moveactivities', 'version'),
        ]));
        $module = (int)$CFG->version < 2025041400 ? 'admin_help_legacy' : 'admin_help';
        $PAGE->requires->js_call_amd('local_moveactivities/admin_help_form', 'init');
        $PAGE->requires->js_call_amd('local_moveactivities/' . $module, 'init', [[
            'topic' => optional_param('moveactivitieshelp', '', PARAM_ALPHA),
        ]]);
    }

    /** Native popover with concise guidance and a direct link to the extended topic. */
    public static function icon(string $key): string {
        global $CFG, $PAGE;
        if (!in_array($key, self::TOPICS, true)) {
            throw new \coding_exception('Unknown Administration help topic.');
        }
        $url = new \moodle_url($PAGE->url);
        $url->param('moveactivitieshelp', $key);
        $help = new \help_icon($key, 'local_moveactivities', $url->out(false));
        $renderer = $PAGE->get_renderer('core');
        $data = (array)$help->export_for_template($renderer);
        $data['completedoclink'] = \html_writer::link($url, get_string('adminhelpmore', 'local_moveactivities'));
        $data['legacy'] = (int)$CFG->version < 2025041400;
        $data['infoicon'] = (int)$CFG->version < 2026100500 ? 'i/info' : 'i/circleinfo';
        return $renderer->render_from_template('local_moveactivities/settings_help_icon', $data);
    }

    /** Always available in the native Topnav user area on the registered page. */
    public static function navbar(\renderer_base $renderer): string {
        global $CFG;
        if (!self::$prepared) {
            return '';
        }
        $label = get_string('adminhelp', 'local_moveactivities');
        return \html_writer::tag('button', $renderer->pix_icon(
            (int)$CFG->version < 2026100500 ? 'i/info' : 'i/circleinfo', ''), [
                'type' => 'button', 'class' => 'nav-link icon-no-margin local-moveactivities-help-navbar',
                'data-moveactivities-help-open' => '', 'aria-label' => $label, 'title' => $label,
                'aria-controls' => 'local-moveactivities-context-help', 'aria-expanded' => 'false',
            ]);
    }

    /** Same header, topic chooser and article layout as the Frontpage reference. */
    public static function render(): string {
        global $PAGE;
        if (!self::$prepared) {
            return '';
        }
        $title = get_string('adminhelp', 'local_moveactivities');
        $close = \html_writer::tag('button', \html_writer::span('&#215;', '', ['aria-hidden' => 'true']), [
            'type' => 'button', 'class' => 'btn btn-secondary btn-sm local-moveactivities-help-close',
            'aria-label' => get_string('closebuttontitle', 'moodle'), 'data-moveactivities-help-close' => '1',
        ]);
        $options = '';
        $articles = '';
        foreach (self::TOPICS as $topic) {
            $label = get_string('adminhelp_' . $topic, 'local_moveactivities');
            $options .= \html_writer::tag('option', $label, ['value' => $topic]);
            $articles .= \html_writer::tag('article', \html_writer::tag('h3', $label, ['class' => 'h5', 'tabindex' => '-1'])
                . get_string('adminhelp_' . $topic . '_article', 'local_moveactivities'), [
                    'data-moveactivities-help-article' => $topic, 'hidden' => 'hidden',
                ]);
        }
        $choice = \html_writer::tag('label', get_string('adminhelptopics', 'local_moveactivities'), [
            'for' => 'local-moveactivities-help-topic', 'class' => 'form-label',
        ]) . \html_writer::tag('select', $options, [
            'id' => 'local-moveactivities-help-topic', 'class' => 'form-select custom-select',
        ]);
        $header = \html_writer::tag('header', \html_writer::tag('h2', $title, [
            'id' => 'local-moveactivities-context-help-title', 'class' => 'sr-only visually-hidden', 'tabindex' => '-1',
        ]) . \html_writer::div($choice, 'local-moveactivities-help-choices') . $close,
            ['class' => 'local-moveactivities-help-header']);
        return \html_writer::tag('aside', $header
            . \html_writer::div($articles, 'local-moveactivities-help-content', ['tabindex' => '0']), [
                'id' => 'local-moveactivities-context-help', 'class' => 'local-moveactivities-context-help',
                'aria-labelledby' => 'local-moveactivities-context-help-title', 'hidden' => 'hidden',
                'data-moveactivities-help-url' => $PAGE->url->out(false),
            ]);
    }

    /** Append the registered panel without changing the page's controls. */
    public static function footer(\core\hook\output\before_standard_footer_html_generation $hook): void {
        $hook->add_html(self::render());
    }
}
