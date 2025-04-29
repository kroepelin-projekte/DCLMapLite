# DCLMapLite - ILIAS Plugin

**Table of Contents**

- [Introduction](#introduction)
- [Compatibility](#compatibility)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Activation](#activation)
- [Usage](#usage)
- [License](#license)

## Introduction

This plugin was specifically developed for the display of an interactive map in the Page Editor.  
Location information, such as addresses and additional details, is managed through a data collection and displayed on the map.  
Each location is marked with a blip on the map, and the corresponding address is made visible.  
Different datasets can be created and managed for each map individually, allowing flexible adaptation to various requirements.


### Compatibility
| Plugin Branches | ILIAS Versions | PHP Versions |
|-----------------|----------------|--------------|
| main            | 9              | 8.1 - 8.2    |


### Prerequisites

- NPM
- leaflet Version 1.8.0

## Installation

1. Launch a terminal instance running `bash` from the project's root directory.
2. Enter the following commands to proceed with the plugin installation.

**Create directories**
```bash
mkdir -p Customizing/global/plugins/Services/COPage/PageComponent/
cd Customizing/global/plugins/Services/COPage/PageComponent/
```

**Clone with SSH**
```bash
git clone git@github.com:kroepelin-projekte/DCLMapLite.git DCLMapLite
```

**Or clone with HTTPS**
```bash
git clone https://github.com/kroepelin-projekte/DCLMapLite.git DCLMapLite
```

**Switch to branch**
```bash
cd DCLMap
git switch release_x
```

**Install dependencies**
```bash
npm install
```

**ILIAS Composer**
```bash
composer du
```

## Activation

1. Sign in to ILIAS with Administrator privileges.
2. Proceed to `Administration » Extending ILIAS » Plugins`
3. Locate the desired plugin, then select `Actions » Install`, and subsequently, `Actions » Activate`.


## Usage
In the first step, a data collection is created. The following fields should be included: Name, Street, Postal Code, City, Contact Person, Website, Email, and Telephone. Subsequently, the tables are populated with values.  
Next, a map is added to a page. The first step is to specify the data collection responsible for this map. Afterwards, the labels for the selected data collection are defined. By clicking the "Update Records" button, new records are loaded into the map.

## License

This project is licensed under the GPL v3 License.