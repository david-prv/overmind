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
 *
 * @author David Dewes <hello@david-dewes.de>
 */
abstract class Schedule
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
    public static function render(string $cwd, string $for): string
    {
        $schedulePlan = $cwd . "/app/tools/interactions.json";

        if (!file_exists($schedulePlan)) {
            die("<h1>A fatal error occurred. $schedulePlan</h1>");
        }

        $interactions = json_decode(file_get_contents($schedulePlan), true);

        $html = "<ul id='interactions' class=\"list-group\">";

        if (isset($interactions[$for]) && count($interactions[$for]) >= 1) {
            $pos = 0;
            foreach ($interactions[$for] as $interaction) {
                $html .= "<li value='$interaction' id='$pos' class=\"list-group-item d-flex justify-content-between align-items-center interaction\">
                            #$pos \"$interaction\"
                            <span><button onclick='(function(event) {
                                  event.stopPropagation();
                                  removeInteraction($pos)
                              })(event);' class=\"btn btn-sm btn-outline-secondary\" type=\"button\">
                                <i class=\"fa fa-trash\"></i>
                            </button>
                            <button onclick='(function(event) {
                                  event.stopPropagation();
                                  moveUp($pos)
                              })(event);' class=\"btn btn-sm btn-outline-secondary\" type=\"button\">
                                <i class=\"fa fa-arrow-up\"></i>
                            </button>
                            <button onclick='(function(event) {
                                  event.stopPropagation();
                                  moveDown($pos)
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

    /**
     * Takes the current working directory and
     * the current tool ID. It searches in the schedule
     * json file for stores interactions. If present
     * the method will return true, otherwise false
     *
     * @param string $cwd
     * @param string $for
     * @return bool
     */
    public static function isPresent(string $cwd, string $for): bool
    {
        $schedulePlan = $cwd . "/app/tools/interactions.json";

        if (!file_exists($schedulePlan)) {
            return false;
        }

        $interactions = json_decode(file_get_contents($schedulePlan), true);
        return isset($interactions[$for]) && count($interactions[$for]) > 0;
    }

    /**
     * Takes the current working directory and a JSON formatted
     * schedule, which will be written into the interactions.json
     * file, located in the tools folder. The old schedule will be
     * overwritten entirely
     *
     * @param string $cwd
     * @param array $schedule
     * @param string $for
     * @return bool
     */
    public static function put(string $cwd, array $schedule, string $for): bool
    {
        $schedulePlan = $cwd . "/interactions.json";

        if (!file_exists($schedulePlan)) {
            return false;
        }

        $stored = json_decode(file_get_contents($schedulePlan), true);

        if (is_null($stored)) {
            return false;
        }

        if (count($schedule) === 1 && $schedule[0] === "") {
            unset($stored[$for]);
        } else {
            $stored[$for] = $schedule;
        }

        return file_put_contents($schedulePlan, json_encode($stored));
    }
}