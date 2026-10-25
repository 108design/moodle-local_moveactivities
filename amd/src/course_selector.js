/**
 * File documentation for amd/src/course_selector.js.
 *
 * @package   local_moveactivities
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license   See LICENSE.md for the full terms.
 */
// This file is part of a 108design source-available software product.
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
// See LICENSE.md for the full terms.

/**
 * AJAX adapter for the native course autocomplete.
 *
 * @module     local_moveactivities/course_selector
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    See LICENSE.md for the full terms.
 */
import Ajax from 'core/ajax';

/** @param {string} selector Element selector. @param {Array} courses Minimal response. @returns {Array} Options. */
export const processResults = (selector, courses) => courses.map(course => {
    const label = document.createElement('span');
    label.textContent = course.label;
    return {value: course.id, label: label.innerHTML};
});

/**
 * Request suggestions only for a useful query; numeric IDs can be a single digit.
 * @param {string} selector Element selector.
 * @param {string} query Search input.
 * @param {Function} success Native autocomplete success callback.
 * @param {Function} failure Native autocomplete failure callback.
 */
export const transport = (selector, query, success, failure) => {
    query = (query || '').trim();
    if (!query || query.length > 100 || (query.length < 2 && !/^\d+$/.test(query))) {
        success([]);
        return;
    }
    Ajax.call([{
        methodname: 'local_moveactivities_search_courses',
        args: {query, purpose: document.querySelector(selector)?.dataset.coursePurpose || ''},
    }])[0].then(success).catch(failure);
};
