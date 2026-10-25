/**
 * This file is part of a 108design source-available software product.
 *
 * Use and modification are permitted only under the Software License included
 * with this distribution. See LICENSE.md for the full terms.
 *
 * File documentation for amd/src/selection.js.
 *
 * @package   local_moveactivities
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license   See LICENSE.md for the full terms.
 */
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>. See LICENSE.md.
/**
 * Native AJAX course controls and scoped activity-selection behaviour.
 * @module local_moveactivities/selection
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license See LICENSE.md for the full terms.
 */
import Autocomplete from 'core/form-autocomplete';
import {getString} from 'core/str';
import Notification from 'core/notification';

export const init = async () => {
    const form = document.getElementById('moveactivities-form');
    if (!form || form.dataset.initialised) return;
    form.dataset.initialised = '1';
    try {
        const placeholder = await getString('coursesearch', 'local_moveactivities');
        const noSelection = await getString('choosedots', 'moodle');
        await Promise.all(['sourcecourseid', 'targetcourseid'].map(async field => {
            await Autocomplete.enhance('#' + field, false, 'local_moveactivities/course_selector', placeholder,
                false, true, noSelection);
            const help = form.querySelector('[data-transfer-help-field="' + field + '"]');
            const input = document.getElementById(field)?.parentElement.querySelector('.form-autocomplete-input');
            if (help && input) {
                const row = document.createElement('div');
                row.className = 'local-moveactivities-control-help';
                input.before(row);
                row.append(input, help);
            }
        }));
    } catch (error) {
        Notification.exception(error);
    }
    form.querySelectorAll('[data-transfer-select]').forEach(button => button.addEventListener('click', () => {
        const checked = button.dataset.transferSelect === 'all';
        form.querySelectorAll('input[name="cmids[]"]').forEach(field => { field.checked = checked; });
    }));
    const action = form.querySelector('[name="actionmode"]');
    const update = () => {
        const isMove = action?.value === 'move';
        form.querySelector('[data-transfer-warning]').hidden = !isMove;
        const button = form.querySelector('[data-transfer-enqueue]');
        button.classList.toggle('btn-danger', isMove);
        button.classList.toggle('btn-primary', !isMove);
    };
    action.addEventListener('change', update);
    update();
};
