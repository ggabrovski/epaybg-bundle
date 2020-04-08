# Symfony bundle for the ePay.bg 

OtobulEpaybgBundle is symfony bundle to help working with ePay.bg communication package for merchants.

Install the package with:

```console
composer require otobul/epaybg-bundle
```

If you're *not* using Symfony Flex, you'll also
need to enable the `Otobul\EpaybgBundle\OtobulEpaybgBundle`
in your `AppKernel.php` file.

## Usage

This bundle provides a controllers, services and templates 
to management 

## Configuration

Configure the bundle in **packages/otobul_epaybg.yaml**
The default values can be listed with:

```
php bin/console config:dump otobul_epaybg
```
To work properly you need also to configure dev and prod env files.  




