# Expectation Maximization in PHP

A command-line demonstration of the expectation-maximization (EM) algorithm. It
simulates observations from a mixture of two coins with fixed head probabilities
of `0.75` and `0.35`, then iteratively estimates those probabilities from the
observations using the supplied initial guesses. Each output row contains the
iteration number and the two current estimates.

## Requirements

- PHP CLI (PHP 7.0 or later)

There are no external package dependencies. Run the commands from this
directory, because `em.php` loads `argparse.php` from the current working
directory.

## Usage

Show the available options:

```sh
php em.php --help
```

Run with initial guesses of `0.5` and `0.9`:

```sh
php em.php -a 0.5 -b 0.9
```

Options:

| Option | Description | Default |
| --- | --- | --- |
| `-a`, `--theta0` | Required initial guess for the first coin's head probability | — |
| `-b`, `--theta1` | Required initial guess for the second coin's head probability | — |
| `-s`, `--sample` | Number of simulated observations and flips per observation | `50` |
| `-i`, `--iterate` | Number of EM iterations | `10` |
| `-h`, `--help` | Display help | — |

For example, to use 100 flips per observation and run 20 iterations:

```sh
php em.php --theta0 0.5 --theta1 0.9 --sample 100 --iterate 20
```

## Files

- `em.php` runs the coin-mixture simulation and EM iterations.
- `argparse.php` provides the command-line argument parser used by the script.

## License

This project is distributed under the GNU General Public License, version 3.
