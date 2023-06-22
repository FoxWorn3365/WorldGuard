# WorldGuard
Protect specific regions from one or more events with **WorldGuard** for PocketMine-MP!<br>

⚠️ We are not in any way related to the [WorldGuard plugin](https://dev.bukkit.org/projects/worldguard) for Bukkit!

## Features
- Possibility of creating regions with different flags
- A lot of flags! (43)
- Region whitelist
- Easy region definition!

## Commands
| Name | Alias | Args | Description |
| --- | --- | --- | --- |
| worldguard | wguard / wg | [info\|pos1\|pos2] | The base command, also used for region definition! |
| region | rg / wrg | [info\|player\|create\|delete\|flags] &lt;REGION NAME&gt; | The region management command |
| flags | none | &lt;REGION NAME&gt; &lt;FLAG&gt; | Add or remove a flag from a region, if the flag is empty displays all flags of the region |

### /region
| Name | Args | Description | Command |
| --- | --- | --- | --- |
| info | &lt;REGION NAME&gt; | Display region's informations | `/region info <REGION NAME>` |
| player | &lt;REGION NAME&gt; &lt;PLAYER&gt; | Add or remove a player from the region's whitelist | `/region player <REGION NAME> <PLAYER>` |
| create | &lt;REGION NAME&gt; | Initialize a region creation, now you'll need to define the area with `/wg pos1`! | `/region create <REGION NAME>` |
| delete | &lt;REGION NAME&gt; | Delete a region | `/region delete <REGION NAME>` |
| flags | &lt;REGION NAME&gt; | Display all region's flags. Kide outdated, instead use `/flags <REGION NAME>` | `/region flags <REGION NAME>` |

### /flags
| Name | Args | Description | Command |
| --- | --- | --- | --- |
| none | none | Displaya all available flags | `/flags` |
| &lt;REGION NAME&gt; | none | Display all flags for the selected region | `/flags <REGION NAME>` |
| &lt;REGION NAME&gt; | &lt;FLAG&gt; | Add or remove a flag from the selected region | `/flags <REGION NAME> <FLAG>` |

## Demonstration Video
On YouTube at [this link](https://youtu.be/GqM56QtxDsA)

## How to: create a region
Defining regions is one of the key parts of the plugin as they then allow you to add or remove flags.<br>
Here are the steps you need to take:
1. Run the `/rg create <NAME>` command to define a new region
2. Now define the region area with `/wg pos1` and `/wg pos2`
> **Note**<br>
> Regions are **2D** so you don't need to define also the height (y)!
3. Manage flags with `/flags <NAME>`

## How to: add a player to the whitelist
Easy:
```
/rg player <REGION NAME> <PLAYER NAME>
```

## Bug reporting
Please report bugs only via **the GitHub issues of this repository and __not in DMs on discord or via email__!**<br>
When you score a bug, please include:
- The download source of the plugin (GutHub or Poggit)
- All files inside the `plugin_data/WorldGuard/` folder
- The __full__ error code
- A simple guide to know how to reproduce the bug
- (Optional) the `WorldGuard.phar`

## Contributing
I welcome any contribution and here are two guides for contribution:
### Translation contribution
If you decided to help with the translation then free to create a pull request where you edit the `src/FoxWorn3365/WorldGuard/languages.json` file and also the `README.md` updating the list of translators by adding the language.<br>
The name must follow [ISO 639-1](https://it.wikipedia.org/wiki/ISO_639) standards or [ISO 639-2](https://it.wikipedia.org/wiki/ISO_639-2) in the case of dialects.<br>
Correct: 
```
[...]
    "roa":{ // The ligurian language, a dialect
      [...]
```
Correct:
```
[...]
    "es":{ // Spanish language, a global and official language
    [...]
```
Wrong:
```
[...]
    "roa-IT":{ // Yes, Ligurian is an Italian dialect but the format is wrong!
    [...]
```
Wrong:
```
[...]
    "spa":{ // Yes, this is also a global language (spanish) BUT because of this it's mandatory to use the ISO 639 standard!
    [...]
```
### Coding contribution
To contribute to the code, it is mandatory to meet these parameters:
- Clean code
- Comment what you do please
- Update the "file header" if you edit the scope or if you create new files
- Please explain what you've changed in the pull request!

## Languages
| Language | Code | Contributors |
| --- | --- | --- |
| English (US) | `en` | [FoxWorn3365](https://github.com/FoxWorn3365) |
| Italian (IT) | `it` | [FoxWorn3365](https://github.com/FoxWorn3365) |

## Useful links
- [Poggit CI page](https://poggit.pmmp.io/ci/FoxWorn3365/WorldGuard/);
- [Poggit](https://poggit.pmmp.io/)

## Contacts
Feel free to contact me via:
- Discord: `@foxworn`
- Email: `foxworn3365@gmail.com`