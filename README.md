# Expectation Maximization Algorithm
This is an implementation of expectation maximization algorithm in PHP.

Usage:

```
usage: em.php [-h/--help] -a/--theta0 THETA0 -b/--theta1 THETA1

The Expectation-Maximization (EM) algorithm is a way to find maximum-likelihood estimates
for model parameters when your data is incomplete, has missing data points, or has
unobserved (hidden) latent variables. It is an iterative way to approximate the maximum
likelihood function. While maximum likelihood estimation can find the best fit model for a
set of data, it doesn't work particularly well for incomplete data sets. The more complex
EM algorithm can find model parameters even if you have missing data. It works by choosing
random values for the missing data points, and using those guesses to estimate a second
set of data. The new values are used to create a better guess for the first set, and the
process continues until the algorithm converges on a fixed point.

Written by Conrad Shyu (conrad.shyu@nih.gov)

optional arguments:
  -h, --help                      Show this help message and exit.
  -a THETA0, --theta0 THETA0      [Required] Theta 0; initial guess.
  -b THETA1, --theta1 THETA1      [Required] Theta 1; initial guess.
  -s SAMPLE, --sample SAMPLE      [Optional] Number of samples. (Default: 50).
  -i ITERATE, --iterate ITERATE   [Optional] Number of iterations. (Default: 10).

example: em.php -a 0.5 -b 0.9
```
