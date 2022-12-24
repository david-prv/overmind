<?php

/**
 * Class Analyzer
 *
 * <p>
 * The Analyzer class is responsible to collect all
 * relevant details from the generated reports of the bundle scanners.
 * The difficulty here is, that all tools have different reports,
 * without any standards. Thus, we needed to find some kind of expression
 * collection on which we can base our assumptions.
 * </p>
 *
 * <p>
 * A final analysis from this class will contain:
 * <ul>
 *      <li>the risk factor (value between 1-100)</li>
 *      <li>general information (wp version, title, ...)</li>
 *      <li>an overview of all found vulnerabilities</li>
 *      <li>an overview of all found information leakage</li>
 * </ul>
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Analyzer
{
    // TODO
}