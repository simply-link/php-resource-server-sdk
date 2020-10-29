# SimplyLink resource server SDK


Resource server is use to hold simplylink resources such as apps and services. 


## Installation:
> security.yml
```
security:
    ...
    
    providers:
        ...
        simplylink_clients:
            id: simplylink.oauth.resource_server.client


    firewalls:
        ...
            
        main:
            ...
            guard:
                authenticators:
                    -  simplylink.oauth.resource_server.guard

```


> config.yml
```
simplylink_resource_server:
    auth_username: 'xxx'
    auth_password: 'xxx'
```


> AppKernel.php
```
    $bundles = [
        ...
        new SimplyLink\ResourceServerBundle\SimplyLinkResourceServerBundle()
    ]
```


