#!/usr/bin/php -d memory_limit=-1

<?php
/*
 * em.php
 *
 * In statistics, an expectation maximization (EM) algorithm is an iterative method for
 * finding maximum likelihood or maximum a posterior (MAP) estimates of parameters in
 * statistical models, where the model depends on unobserved latent variables. The EM
 * iteration alternates between performing an expectation (E) step, which creates a
 * function for the expectation of the log-likelihood evaluated using the current
 * estimate for the parameters, and a maximization (M) step, which computes parameters
 * maximizing the expected log-likelihood found on the E step. These parameter estimates
 * are then  used to determine the distribution of the latent variables in the next E
 * step.
 *
 * note: larger samples permit parameter estimates to converge much faster simply
 * because the algorithm iterates through the same training dataset over and over again.
 * if the training dataset does not possess enough discriminant power, increased number
 * of iterations will not improve convergence.
 *
 * Written by Conrad Shyu (conradshyu@yahoo.com)
 *
 * Revised on June 7, 2016
 * Revised on November 25, 2020
*/

require_once "argparse.php";

define("THETA1", 0.75);     // actual probability for first coin
define("THETA2", 0.35);     // actual probability for second coin

$r = function() {
    return(mt_rand() / mt_getrandmax());
};  // generate a uniform random number

$fx = function($r, $p = 0.5, $n = 50) {
    return(array_sum(array_map(function() use ($p, $r) {
        return(($r() < $p) ? 1 : 0);}, array_fill(0, $n, 0))));
};  // coin flip experiment

$bd = function ($p = 0.5, $n = 100, $k = 50) {
    return(($n == $k) ? exp($n * log(1 - $p)) : exp(
        array_sum(array_map(function($i) {return(log($i));}, range($k + 1, $n))) -
        array_sum(array_map(function($i) {return(log($i));}, range(1, $n - $k))) +
        $k * log($p) + ($n - $k) * log(1.0 - $p)));
};  // calculate the probability based on binomial distribution

/*
$bd = function ($p = 0.5, $n = 100, $k = 50) {
    return(pow($p, $k) * pow(1.0 - $p, $n - $k) * (($k == 0) ? 1.0 :
        array_product(array_map(function($a, $b) {return($a / $b);},
        range($n - $k + 1, $n, 1), range(1, $k, 1)))));
};  // calculate the probability based on binomial distribution
*/

$preface = <<<'EOT'
The Expectation-Maximization (EM) algorithm is a way to find maximum-likelihood estimates
for model parameters when your data is incomplete, has missing data points, or has
unobserved (hidden) latent variables. It is an iterative way to approximate the maximum
likelihood function. While maximum likelihood estimation can find the best fit model for a
set of data, it doesn't work particularly well for incomplete data sets. The more complex
EM algorithm can find model parameters even if you have missing data. It works by choosing
random values for the missing data points, and using those guesses to estimate a second
set of data. The new values are used to create a better guess for the first set, and the
process continues until the algorithm converges on a fixed point.

Written by Conrad Shyu (conradshyu@yahoo.com)
EOT;

$epilog = <<<'EOT'
example: em.php -a 0.5 -b 0.9
EOT;

$cmd = new ArgParser(basename(__FILE__), $preface, $epilog);
$cmd->add("-a", "--theta0", "theta0", 0.0, true, "Theta 0; initial guess.");
$cmd->add("-b", "--theta1", "theta1", 0.0, true, "Theta 1; initial guess.");
$cmd->add("-s", "--sample", "sample", 50, false, "Number of samples.");
$cmd->add("-i", "--iterate", "iterate", 10, false, "Number of iterations.");

if (!$cmd->parse()) {
    return(false);
}   // make sure all parameters are available

for ($i = 0; $i < $cmd->get("iterate"); $i++) {
    $smp = $cmd->get("sample");     // number of samples
    $t1 = $cmd->get("theta0"); $t2 = $cmd->get("theta1");   // initial guess of the values

    $l = array_map(function() use ($fx, $r, $smp) {
            return($fx($r, ($r() > 0.5) ? THETA1 : THETA2, $smp));},
        array_fill(0, $smp, 0));    // generate training dataset
    $p0 = 0; $p1 = 0; $q0 = 0; $q1 = 0;

    foreach ($l as $k) {
        $a = $bd($t1, $smp, $k); $b = $bd($t2, $smp, $k);
        // calculate the likelihood with binomial distribution
        $p0 += ($k * $a / ($a + $b)); $q0 += ($k * $b / ($a + $b));
        // normalize the probabilities for head outcomes

        $a = $bd(1.0 - $t1, $smp, $smp - $k); $b = $bd(1.0 - $t2, $smp, $smp - $k);
        // calculate the likelihood with binomial distribution
        $p1 += (($smp - $k) * $a / ($a + $b)); $q1 += (($smp - $k) * $b / ($a + $b));
        // normalize the probabilities for tail outcomes
    }   // accumulate the expectations

    printf("%4d, %.8f, %.8f\n", $i + 1, $p0 / ($p0 + $p1), $q0 / ($q0 + $q1));
}   // run expectation maximization algorithm

?>
