<?php

/**
 * Class Schedule
 *
 * <p>
 * The Schedule class is responsible for updating/setting
 * new interaction schedules for specific tools.
 * This feature will be used mainly by the Core class, which
 * processes back-end HTTP requests.
 * </p>
 *
 * <p>
 * The use of the Schedule is to keep a list of
 * inputs/commands for runner.py. The runner script then
 * uses the inputs in the given order as inputs. This is especially useful
 * for tools, which allow user-generated input.
 * </p>
 */
class Schedule
{
    /**
     * Takes the current working directory and the
     * current tool ID. Then, the method generates a
     * html list with handlers to manage the stored interactions
     * from interactions.json (= the "database")
     *
     * @param string $cwd
     * @param string $for
     * @return string
     */
    public static function html(string $cwd, string $for): string {
        $schedulePlan = $cwd. "/app/tools/interactions.json";

        if (!file_exists($schedulePlan)) {
            die("<h1>A fatal error occurred. $schedulePlan</h1>");
        }

        $interactions = json_decode(file_get_contents($schedulePlan), true);

        $html = "<ul id='interactions' class=\"list-group\">";

        if(isset($interactions[$for]) && count($interactions[$for]) >= 1) {
            $pos = 0;
            foreach ($interactions[$for] as $interaction) {
                $html .= "<li id='$pos' class=\"list-group-item d-flex justify-content-between align-items-center interaction\">
                            #$pos \"$interaction\"
                            <span><button onclick='(function(event) {
                                  event.stopPropagation();
                                  removeInteraction($pos)
                              })(event);' class=\"btn btn-sm btn-outline-secondary\" type=\"button\">
                                <i class=\"fa fa-trash\"></i>
                            </button>
                            <button onclick='(function(event) {
                                  event.stopPropagation();
                                  // TODO
                              })(event);' class=\"btn btn-sm btn-outline-secondary\" type=\"button\">
                                <i class=\"fa fa-arrow-up\"></i>
                            </button>
                            <button onclick='(function(event) {
                                  event.stopPropagation();
                                  // TODO
                              })(event);' class=\"btn btn-sm btn-outline-secondary\" type=\"button\">
                                <i class=\"fa fa-arrow-down\"></i>
                            </button></span>
                          </li>";
                $pos++;
            }
        } else $html .= "<h2 class='text-muted text-center'>No interactions found</h2>";

        $html .= "</ul>";
        return $html;
    }
}