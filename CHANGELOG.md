## [3.1.0] - 2025-01-15

- Implicitly marked parameter as nullable in Url class.
- Fixed EmailAddress so local part can not start or end with a dot.

## [3.0.0] - 2022-03-14

- Added ParsableDataTypeInterface.
- Changed required PHP version to >= 8.0.
- Removed deprecated namespaces and classes.

## [2.4.0] - 2021-09-01

- Moved all classes and interfaces to DataTypes/Common, DataTypes/Net and DataTypes/System namespaces. Other DataTypes namespaces are deprecated.

## [2.3.0] - 2021-02-10

- Changed required PHP version to >= 7.3.

## [2.2.0] - 2020-12-22

- Added withFilename method to FilePath and UrlPath classes.
- Added parseAsDirectory and tryParseAsDirectory methods to FilePath and UrlPath classes.
- Changed required PHP version to >= 7.2.

## [2.1.0] - 2019-06-25

- Added getPathAndQueryString method to Url class.

## [2.0.0] - 2017-12-25

- Added type hints to all data type classes.
- Changed required PHP version to >= 7.1.

## [1.2.0] - 2017-12-22

- Added equals method to all data type classes.
- Added toInteger method to IPAddress class.
- Added fromInteger method to IPAddress class.

## [1.1.0] - 2017-10-01

- Added EmailAddress class.

## [1.0.1] - 2017-05-29

- Fixed [#4](https://github.com/themichaelhall/datatypes/issues/4) - Url with name and/or password should not be treated as invalid.

## 1.0.0 - 2017-05-23

- First stable revision.

[3.1.0]: https://github.com/themichaelhall/datatypes/compare/v3.0.0...v3.1.0
[3.0.0]: https://github.com/themichaelhall/datatypes/compare/v2.4.0...v3.0.0
[2.4.0]: https://github.com/themichaelhall/datatypes/compare/v2.3.0...v2.4.0
[2.3.0]: https://github.com/themichaelhall/datatypes/compare/v2.2.0...v2.3.0
[2.2.0]: https://github.com/themichaelhall/datatypes/compare/v2.1.0...v2.2.0
[2.1.0]: https://github.com/themichaelhall/datatypes/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/themichaelhall/datatypes/compare/v1.2.0...v2.0.0
[1.2.0]: https://github.com/themichaelhall/datatypes/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/themichaelhall/datatypes/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/themichaelhall/datatypes/compare/v1.0.0...v1.0.1
