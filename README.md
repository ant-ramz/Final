# CS 85 - FINAL / "@ProductionPlanner"

## Project Description / Features
-This software is meant to simulate Production Planning Software. The user enters their Inventory, and then their Products that are made up of Inventory items. From there, the user will then enter a Prodcution Plan (entering how much of the Product they wish to schedule to create). OpenAI will cross reference the inventory with the production plan and alert the user on the amount of inventory they need to procure, if any.

---

## Setup Instructions

### 1. Prerequisites
- **Windows / Mac** with Laravel Herd installed
- **Open AI Account and Functional API Key**

### 2. Download Files
- Extract Folder once the files have downloaded.

### 3. Set Up Environment File
- Located in the main directory of the downloaded ziped folder, open the file '.env', and scroll to the very end. You should see the following code:

      # OpenAI Configuration
      OPENAI_API_KEY=your_openai_api_key_here
      OPENAI_API_URL=https://api.openai.com/v1
      OPENAI_MODEL=gpt-3.5-turbo

  - Replace 'your_openai_api_key_here' with your unique Open AI API key.
 
### 4. Run The Application WINDOWS
- Open CMD 
- Move into your Laravel project folder (type the following code into the CMD)

      cd C:\YOUR DIRECTORY\project-planner

      Replace 'YOUR DIRECTORY' with the directory where your files have been saved at

- Start the Laravel Server by entering the following code into the CMD:

      php artisan serve

- Enter the following address into your web browser:

    http://127.0.0.1:8000/

## Troubleshooting

- If php artisan serve fails, enter the following code:

          php -S 127.0.0.1:8000 -t public

- Enter the following address into your web browser:

    http://127.0.0.1:8000/

### Site Launched

- With the site now launched, enter a prompt for Open AI to generate a blog post.


### 4. Run The Application MAC
- Open Terminal
- Move into your Laravel project folder (type the following code into the CMD)

      cd ~/YOUR DIRECTORY/project-planner

      Replace 'YOUR DIRECTORY' with the directory where your files have been saved at

- Start the Laravel Server by entering the following code into the Terminal:

      php artisan serve

- Enter the following address into your web browser:

    http://127.0.0.1:8000/

### Troubleshooting

- If php artisan serve fails, enter the following code:

          php -S 127.0.0.1:8000 -t public

- Enter the following address into your web browser:

    http://127.0.0.1:8000/

### Site Launched

- With the site now launched, you can enter inventory, create a product, and then create a production plan for that product.

 ![1.png](https://github.com/ant-ramz/Final/blob/main/1.png)
 ![2.png](https://github.com/ant-ramz/Final/blob/main/2.png)
 ![3.png](https://github.com/ant-ramz/Final/blob/main/3.png)
 ![4.png](https://github.com/ant-ramz/Final/blob/main/4.png)
