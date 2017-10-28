# API DOC

## Voting List

### List

__GET__ `/api/votings`


#### Response

__200__

    {
        "votings": [
            {
                "id": 1,                                            (required)
                "status": "active",                                 (required, {"archived" | "active" | "future"})            
                "title": "string title",                            (required)
                "short_description": "string of short description", (optional)
                "description": "string description",                (optional)
                "location": "Cherkasy",                             (required)    
                "max_votes_count": 4,                               (required)
                "date_from": "2017-09-23T18:14:15+00:00",           (required)
                "date_to": "2017-09-25T18:14:15+00:00",             (required)
                "logo": "http://imisto.com.ua/img/logo.png",        (optional)
                "background_image": "http://imisto.com.ua/bg.png",  (optional)
                "voted": 7654                                       (required)
            }
        ]
    }


## Projects

### List

__GET__ `/api/votings/{voting_id}/projects`

#### Response

__200__

    {
        "projects": [{
            "is_voted": "true/false"
            "id": 1,
            "title": "test title",
            "description": "test description",
            "charge": "charge for project",            
            "source": "where",
            "picture": "<url-picture>",
            "createdAt": "2015-10-03T09:11:04+00:00",
            "likes_count": 123,
            "owner": "fullName",
            "avatar_owner": "avatar owner",
            "voted": 95
        }]
    }

### One project

__GET__ `/api/votings/{voting_id}/projects/{project_id}`

### Response

__200__

    {
        "project": {
            "is_voted": "true/false"
            "id": 1,
            "title": "test title",
            "description": "test description",
            "charge": "charge for project",            
            "source": "where",
            "picture": "<url-picture>",
            "createdAt": "2015-10-03T09:11:04+00:00",
            "likes_count": 123,
            "owner": "fullName",
            "avatar_owner": "avatar owner",    
            "voted": 95   
        }
    }

## Auth Settings 

__GET__ `/api/settings`    

### Response

__200__

    {
        "bi_auth_url": "https:\/\/bankid.org.ua",
        "bi_client_id": "string client id,
        "bi_redirect_uri": "https:\/\/vote.imisto.com.ua\/api\/login"
    }

## Like project

### Vote

__POST__ `/api/votings/{voting_id}/projects/{project_id}/vote`
          `{voting_id}  - voting id`
          `{project_id} - id project`

__200__

    {
        "success": "Ваший голос зараховано на підтримку проект."
    }
    
__401__

    {
        "warning": "Ви не маєте доступу до голосуваня за проект."
    }

__403__

    {
        {
            "warning": "Проект без налаштувань голосування."
        }

        {
            "warning": "Вибачте. Кінцева дата голосування до DATE"
        }

        {
            "warning": "Вибачте. Голосування розпочнеться DATE"
        }

        {
            "warning": "Ви вже вичерпали свій ліміт голосів."
        }

        {
            "warning": "Ви вже підтримали цей проект."
        }
        
        {
            "warning": "Цей проект не стосується міста в якому ви зареєстровані."
        }
    }

__404__

    {
        "warning": "Проект не знайдено для даного голосування"
    }

## User

### Authorization user

__GET__ `/api/authorization?code=idSlnv44178`

__POST__ `/api/authorization`

#### Request

    {
        "code": <bankId code>
    }

#### Response

__200__

    {
        "user": {
            "id": 111
            "fullName": <fullName of user>,
            "clid": <very secret key>,
            "voted_project": <id project>/false
        }
    }

__401__

    {
        "error": {
            "code": 401,
            "message": "Wrong authorization."
        }
    }
