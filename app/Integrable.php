<?php

/**
 * Interface Integrable
 *
 * <p>
 * An Integrable is a class which is able to integrate/delete/update itself
 * into/from/in the scanner bundle set. The Scanner for example implements both,
 * the Runnable and the Integrable, since a Scanner can run and integrate
 * itself, without any helper classes.
 * </p>
 *
 * <p>
 * This interface intentionally overrides / reuses already existing
 * methods from a Runnable. This is because a Runnable is a Integrable,
 * vice-versa, but a Scanner can also be only a Integrable and also only
 * a Runnable. There is no need that a Scanner class implements both.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
interface Integrable
{
    /**
     * Defines the scanner name
     *
     * @param string $name
     * @return Integrable
     */
    public function hasName(string $name): Integrable;

    /**
     * Defines the creator's name
     *
     * @param string $creator
     * @return Integrable
     */
    public function fromCreator(string $creator): Integrable;

    /**
     * Defines the creator's reference url
     *
     * @param string $url
     * @return Integrable
     */
    public function setCreatorURL(string $url): Integrable;

    /**
     * Defines the version the tool is currently in
     *
     * @param string $version
     * @return Integrable
     */
    public function inVersion(string $version): Integrable;

    /**
     * Defines the startup arguments used for the Runnable
     *
     * @param string $args
     * @return Integrable
     */
    public function withArguments(string $args): Integrable;

    /**
     * Defines the running engine used
     * as interpreter by the python runner
     *
     * @param string $engine
     * @return Integrable
     */
    public function viaEngine(string $engine): Integrable;

    /**
     * Defines the current working directory
     * for the integration process
     *
     * @param string $cwd
     * @return Integrable
     */
    public function useCWD(string $cwd): Integrable;

    /**
     * Defines the tool description
     *
     * @param string $description
     * @return Integrable
     */
    public function describedBy(string $description): Integrable;

    /**
     * Defines the POST file data
     *
     * @param array $data
     * @return Integrable
     */
    public function fileData(array $data): Integrable;

    /**
     * Performs the final integration
     *
     * @return bool
     */
    public function integrate(): bool;

    /**
     * Deletes the given tool/scanner from the
     * bundle using the given ID
     *
     * @return bool
     */
    public function delete(): bool;

    /**
     * Updates the given fields for the given
     * tool/scanner, which is identified by the ID
     *
     * @return bool
     */
    public function update(): bool;
}