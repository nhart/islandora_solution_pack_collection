<?php

/**
 * @file
 * Hook documentation
 */

/**
 * Hook to get a number of Sparql statements, to build the collection query.
 *
 * @return array|string
 *   Either an array containing multiple or a string containing a single
 *   Sparql statement. This is to build up the tuples available to be filtered.
 *   There are a number of placeholders which may be included for replacement:
 *   - "!pid": The identifier of the collection object, as
 *     "namespace:local-name" (no "info:fedora/" bit).
 *   - "!model": A string representing a URI. Defaults to "?model", but could
 *     be provided as "<info:fedora/cmodel:pid>" if the type of object to query
 *     should be filtered.
 */
function hook_islandora_basic_collection_get_query_statements() {
  return <<<EOQ
  ?object ?collection_predicate <info:fedora/!pid> ;
          <fedora-model:label> ?title ;
          <fedora-model:hasModel> !model ;
          <fedora-model:state> <fedora-model:Active>
EOQ;
}

/**
 * Hook to get a number of optional Sparql statements.
 *
 * Really, this shouldn't be necessary--they should be able to be included as
 * normal statements... There's a bug in Mulgara which prevents OPTIONAL
 * statements from working properly, though.
 *
 * Be mindful if involving two different variables being "SELECTED"/
 * returned in results, as the manner in which these statements get unioned
 * together could end up with apparently duplicate results: both where one
 * variable is bound and another where the variable is unbound.
 *
 * @return array|string
 *   Either an array containing multiple or a string containing a single
 *   Sparql statement. This is to build up the tuples available to be filtered.
 *   There are a number of placeholders which may be included for replacement:
 *   - "!pid": The identifier of the collection object, as
 *     "namespace:local-name" (no "info:fedora/" bit).
 *   - "!model": A string representing a URI. Defaults to "?model", but could
 *     be provided as "<info:fedora/cmodel:pid>" if the type of object to query
 *     should be filtered.
 */
function hook_islandora_basic_collection_get_query_optionals() {
  // Taken from islandora_compound_object, this would probably be used with a
  // filter checking if "?compound" is bound.
  return "?object <fedora-rels-ext:isConstituentOf> ?compound";
}

/**
 * Hook to modify query.
 */
function islandora_basic_collection_query_param_alter(array $filters, array $statements, array $params, array $optionals) {
}

/**
 * Hook to get a number of Sparql filters, to build the collection query.
 *
 * @return array|string
 *   Either an array containing multiple or a string containing a single
 *   Sparql filter. This is to reduce the tuples to return, as selected with
 *   hook_islandora_basic_collection_get_query_statements(). There are a number
 *   of placeholders which may be included for replacement:
 *   - "!pid": The identifier of the collection object, as
 *     "namespace:local-name" (no "info:fedora/" bit).
 *   - "!model": A string representing a URI. Defaults to "?model", but could
 *     be provided as "<info:fedora/cmodel:pid>" if the type of object to query
 *     should be filtered.
 */
function hook_islandora_basic_collection_get_query_filters() {
  return 'sameTerm(?collection_predicate, <fedora-rels-ext:isMemberOfCollection>) || sameTerm(?collection_predicate, <fedora-rels-ext:isMemberOf>)';
}

/**
 * Hook into the manage object page.
 *
 * @param array $form_state
 *   Current form state.
 * @param AbstractObject $object
 *   Form object.
 */
function hook_islandora_basic_collection_build_manage_object($form_state, $object) {
  // Example implementation.
  $form_state['manage_collection_object']['manage_obj_lock'] = array(
    '#id' => 'manage-obj-lock',
    '#group' => 'manage_obj_object',
    '#access' => TRUE,
    '#type' => 'fieldset',
    '#title' => t('Manage lock objects'),
    'form' => drupal_get_form('islandora_object_lock_length_manage_lock_form', $object),
  );
  $form_state['manage_collection_object']['manage_obj_lock']['form']['#submit'][] = 'islandora_object_lock_length_manage_lock_form_submit';
  return $form_state;
}

/**
 * Allow altering of the built query.
 *
 * Note that this is only called after the query is fully build, so changes to
 * those parameters associated with placeholders (model, vars, pid, etc) will
 * not have any effect on the outcome.
 *
 * @param array $params
 *   An associative array containing associated information about the query,
 *   including everything passed in the the
 *   islanora_basic_collection_get_query_info() as parameters, with a minimum
 *   of:
 *   - object: The AbstractObject for representing the collection we for which
 *     we are querying.
 *   - page_size: An integer representing the number of items per page.
 *   - page_number: An integer representing which page we are on.
 *   - vars: A string listing the variables which will comprise the types
 *     returned.
 *   - order_by: A string indicating which variable by which to sort. Defaults
 *     to "?title". May be set to FALSE to avoid sorting.
 *   - model: A string representing a URI. Defaults to "?model", but could
 *     be provided as "<info:fedora/cmodel:pid>" if the type of object to query
 *     should be filtered.
 *   - query: The fully built query.
 *   - type: The type of the query ('sparql' by default).
 *   - pid: The identifier associated with 'object'.
 */
function hook_islandora_basic_collection_query_alter(array &$params) {
}
