# Indigo Base

[![Packagist Version](https://img.shields.io/packagist/v/indigophp/base.svg?style=flat-square)](https://packagist.org/packages/indigophp/base)
[![Total Downloads](https://img.shields.io/packagist/dt/indigophp/base.svg?style=flat-square)](https://packagist.org/packages/indigophp/base)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

**IndigoPHP Base component.**


## Install

Via Composer

``` bash
$ composer require indigophp/base
```


## Usage

If not using with the [indigophp/indigophp](https://github.com/indigophp/indigophp) repo put this into `public/index.php`

``` php
$this->router->get('themes/{segment}/{any}')
	->filters([
		'controller' => 'Indigo\Common\Controller\Assets',
		'action' => 'index',
	]);
```


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [Tamás Barta](https://github.com/TamasBarta)
- [Márk Sági-Kazár](https://github.com/sagikazarmark)
- [All Contributors](https://github.com/indigophp/base/contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
