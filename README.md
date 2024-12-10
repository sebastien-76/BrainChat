# projet BrainChat s4 simplon
<hr />

Install the Application

    - Clone the GitHub repository
    
    - Configure your environment variables in the .env file :    
      
      => DATABASE_URL="mysql://username:password@127.0.0.1:3306/scores?serverVersion=8.0.32&charset=utf8mb4"
      
    - Download and install the project dependencies using the following Composer command : composer install
    
    - Create the database using the following command : php bin/console doctrine:database:create
    
    - Set up the database structure with the following command : php bin/console doctrine:migrations:migrate
    
    - Load fixtures to populate the database with sample data using this command : php bin/console doctrine:fixtures:load




Launch the Application
	    
    - Start the Symfony server : symfony server:start 
    
    - You can now access the application at http://localhost:8000
    
    - Use tools like Postman to make HTTP requests  
    
<hr />



Default User

You can use the following credentials to authenticate:
	    
  {
  	"username": "",
 	"password": "123"
	}

   
