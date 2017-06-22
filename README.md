# Instagrizzle

**Fetch media from public Instagram feeds.**

## Installation

Simply copy the `Instagrizzle` folder into `site/addons/`. That's it!

## Usage

Full example
```html
{{ instagrizzle username="aryehraber" limit="5" offset="1" }}
    <a href="{{ link }}">
        <img src="{{ image }}" alt="{{ caption:title }}">
    </a>
{{ /instagrizzle }}
```

Set the Instagram username
```html
{{ instagrizzle username="aryehraber" }}
```

Shorthand
```html
{{ instagrizzle:aryehraber }}
```

Short-Shorthand (if a site-wide default is set in [Settings](#settings))
```html
{{ instagrizzle }}
```

### Optional Parameters

Limit the number of items returned
```html
limit="5"
```

Offset the items returned
```html
offset="1"
```

## Settings

The settings page is accessed via `CP > Configure > Addons > Instagrizzle`.

* **Username** (string): This is used when no `username` parameter is supplied in the Instagrizzle tag.
* **Cache Length** (int) [ *Default: 60* ]: The number of minutes that Instagrizzle should cache Instagram's response.

## Debug

Use the debug tag to output all of Instagram's response data.
```html
{{ instagrizzle:debug username="aryehraber" }}
```