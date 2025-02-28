# Tool for inspecting the Storyblok Space configuration

This tool is designed to simplify the inspection of Storyblok Space configurations, providing developers a Markdown report to explore and understand their Storyblok Spaces better.

## How to use SB Lens

### Requirements
- PHP 8.4 or 8.3
- Composer for installing packages and dependencies

### Install and setup locally

Clone locally the repo, enter into the new directory and install packages.

```shell
git clone https://github.com/robyblok/sb-lens.git
cd sb-lens
composer install
```

Create your `.env` file where you should store your Personal Access Token:

```ini
STORYBLOK_OAUTH_TOKEN="your personal oauth token"
```

Now you can run the sb-lens script with the `check` command and defining the space id with the `-s` argument:

```shell
php sb-lens check -s "your_space_id"
```

You should obtain a report on the standard output in Markdown format.
You can copy and paste it, or you can redirect the output in a file for example:

```shell
php sb-lens check -s "your_space_id" > my-report.md
```