# BIIGLE

The Bio-Image Indexing and Graphical Labelling Environment (BIIGLE) is a web service for the efficient and rapid annotation of still images and videos. Read <a href="https://doi.org/10.3389/fmars.2017.00083">the paper</a> or take a look at <a href="https://biigle.de/manual">the manual</a>.

BIIGLE is available at [biigle.de](https://biigle.de).

## Installation

### Requirements

- [Docker](https://docs.docker.com/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### For Production

Run `./build.sh` to build the `biigle/app`, `biigle/web` and `biigle/worker` Docker images.

Now head over to [biigle/distribution](https://github.com/biigle/distribution) to configure and build your production setup.

### For Development

Take a look at [`DEVELOPING.md`](DEVELOPING.md) for a detailed explanation on how to develop BIIGLE.

## Contributions and bug reports

Contributions to BIIGLE are always welcome. Check out [`CONTRIBUTING.md`](CONTRIBUTING.md) to get started.
