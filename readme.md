![cover](https://raw.githubusercontent.com/alexx855/hackathon-wc-nft/main/cover.png)
## Project description

This project was made on a weekend for the EHTGlobal road to web3 hackathon, its basically a plugin to sell and mint NFTs from WooCommerce Products, it remplaces the default WC downloads panel from where you can see and transfer your current NFTs
I have created a custom plugin for WordPress which allows anyone to buy and mint NFTs directly from a WordPress install and pay for it using any supported gateway in WooCommerce .
This was made using and extending the WordPress & WoooCommerce Core, the theme used is the official from WooCommerce https://woocommerce.com/storefront/ with minor style changes and an user icon added at the top to make the navigation to the user account easier.
The project idea came to my after some research about the ethglobal road to web3 2022 hackathon sponsors, so i decided to build this on Polygon using the NFTPort.xyz API for mint and get the NFTs data, and the Moralis.io APIs to connect to the user from the front end and to make the transfer of the NFTs directly from the website UI . 

### This plugin changes 3 main things:
- it allows you to sell the traditional WooCommerce downloads product as NFTs, keeping all compatibility with WC 3rd party plugin
- it adds a new NFTs tab on the WC user account (/my-account by default) page that remplaces the defaults downloads tab with a custom UI, this its not linked to the store database as it is traditionally, it show your current NFTs on Polygon, you can transfer NFTs directly from the UI in this page.
- it adds a new setting smenu to your WordPress Admin panel from where you should add your NFTPort.xyz and Moralis.io  APIs credentials

### Wordpress Plugins required
Only WooCommerce, EthPress if you want to login with your wallet, not needed but recommended

### Run the project
 create and .env file 
```touch .env```
with the following variables:
```
AUTH_KEY=
SECURE_AUTH_KEY=
LOGGED_IN_KEY=
NONCE_KEY=
AUTH_SALT=
SECURE_AUTH_SALT=
LOGGED_IN_SALT=
NONCE_SALT=
```
generate wordpress salts from https://api.wordpress.org/secret-key/1.1/salt/

build and run the demo:
```docker compose up --build```
or 
```docker-compose up```
