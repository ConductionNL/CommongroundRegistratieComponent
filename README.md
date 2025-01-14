# Commonground Registratie Component

Description
----
This component handles the registration of other components, applications, organisations and APIs into de common-ground.dev website.

Additional Information
----

- [Contributing](CONTRIBUTING.md)
- [ChangeLogs](CHANGELOG.md)
- [RoadMap](ROADMAP.md)
- [Security](SECURITY.md)
- [Licence](LICENSE.md)


Installation
----
We differentiate between two ways of installing this component, a local installation as part of the provided developers toolkit or an [helm](https://helm.sh/) installation in a development or production environment.

#### Local installation
First, make sure you have [docker desktop](https://www.docker.com/products/docker-desktop) running on your computer. Then clone the repository to a directory on your local machine through a [git command](https://github.com/git-guides/git-clone) or [git kraken](https://www.gitkraken.com) (ui for git). If successful, you can now navigate to the directory of your cloned repository in a command prompt and execute docker-compose up.
```CLI
$ docker-compose up
```
This will build the docker image and run the used containers. When seeing the log from the PHP container: "NOTICE: ready to handle connections", you are ready to view the documentation at localhost on your preferred browser.

#### Installation on Kubernetes or Haven
As a Haven-compliant commonground component this component is installable on Kubernetes through Helm. You can find the Helm files in the api/helm folder. For installing this component through helm simply open your (still) favorite command line interface and run
```CLI
$ helm install [name] ./api/helm --kubeconfig kubeconfig.yaml --namespace [name] --set settings.env=prod,settings.debug=0,settings.cache=1
```
For an in-depth installation guide, you can refer to the [installation guide](INSTALLATION.md). It also contains a short tutorial on getting your cluster ready to expose your installation to the world

Standards
----

This component adheres to international, national, and local standards (in that order). Notable standards are:

- Any applicable [W3C](https://www.w3.org) standard, including but not limited to [rest](https://www.w3.org/2001/sw/wiki/REST), [JSON-LD](https://www.w3.org/TR/json-ld11/) and [WEBSUB](https://www.w3.org/TR/websub/)
- Any applicable [schema](https://schema.org/) standard
- [OpenAPI Specification](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md)
- [GAIA-X](https://www.data-infrastructure.eu/GAIAX/Navigation/EN/Home/home.html)
- [Publiccode](https://docs.italia.it/italia/developers-italia/publiccodeyml-en/en/master/index.html), see the [publiccode](api/public/schema/publiccode.yaml) for further information
- [Forum Stanaardisatie](https://www.forumstandaardisatie.nl/open-standaarden)
- [NL API Strategie](https://docs.geostandaarden.nl/api/API-Strategie/)
- [Common Ground Realisatieprincipes](https://componentencatalogus.commonground.nl/20190130_-_Common_Ground_-_Realisatieprincipes.pdf)
- [Haven](https://haven.commonground.nl/docs/de-standaard)
- [NLX](https://docs.nlx.io/understanding-the-basics/introduction)
- [Standard for Public Code](https://standard.publiccode.net/), see the [compliancy scan](publiccode.md) for further information.

Developer's toolkit and technical information
----
We make our data models with the tool [modelio](https://www.modelio.org), found along the OAS documentation and the Postman collection in api/public/schema.
If you need development support we provide that through the [samenorganiseren slack channel](https://join.slack.com/t/samenorganiseren/shared_invite/zt-dex1d7sk-wy11sKYWCF0qQYjJHSMW5Q).

A couple of quick tips when you start developing
- If you haven't set up the component locally, read the Installation part for setting up your local environment.
- You can find the other components on [Github](https://github.com/ConductionNL).
- Take a look at the [commonground componenten catalogus](https://componentencatalogus.commonground.nl/componenten?) to prevent development collisions.
- Use [Commongroun.conduction.nl](https://commonground.conduction.nl/) for easy deployment of test environments to deploy your development to.
- For information on how to work with the component, you can refer to the tutorial [here](TUTORIAL.md).


Contributing
----
First of all, please read the [Contributing](CONTRIBUTING.md) guideline ;)

But most importantly, welcome! We strive to keep an active community at [commonground.nl](https://commonground.nl/). Please drop by and tell us what you are thinking about so that we can help you along.


Credits
----

Information about the authors of this component can be found [here](AUTHORS.md)





Copyright © [Utrecht](https://www.utrecht.nl/) 2019
