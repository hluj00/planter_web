# Smart planter - planter_web

This repository is part of a smart planter project that consists of three parts.
- [planter_planter](https://github.com/hluj00/planter_planter)
- [planter_web](https://github.com/hluj00/planter_web)
- [planter_connector](https://github.com/hluj00/planter_connector)

planter_web is web interface for the smart planter

## Configuration
This web app requires a MySQL database schema and create script can be found in folder /db

MySQL connection and other configurations are done in /app/.env file .env.example is provided as well

## Installation

Before installation make sure you configured file .env 

projec cand by run with docker-compose with simple command
```bash
docker-compose up -d --build
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)