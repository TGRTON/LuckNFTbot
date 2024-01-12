<p align="center">
  <h3 align="center">LuckNFTbot - Telegram Bot</h3>
  <p align="center">
    A Comprehensive Guide to Kickstart Your Telegram Bot Development!
    <br/>
    <a href="https://github.com/TGRTON/LuckNFTbot"><strong>Explore the Documentation »</strong></a>
    <br/>
    <a href="https://github.com/TGRTON/LuckNFTbot">View Demo</a>
    ·
    <a href="https://github.com/TGRTON/LuckNFTbot/issues">Report Bug</a>
    ·
    <a href="https://github.com/TGRTON/LuckNFTbot/issues">Request Feature</a>
  </p>
</p>

![Downloads](https://img.shields.io/github/downloads/TGRTON/LuckNFTbot/total)
![Contributors](https://img.shields.io/github/contributors/TGRTON/LuckNFTbot?color=dark-green)
![Issues](https://img.shields.io/github/issues/TGRTON/LuckNFTbot)
![License](https://img.shields.io/github/license/TGRTON/LuckNFTbot)

## Table Of Contents
* [About the Project](#about-the-project)
* [Built With](#built-with)
* [Getting Started](#getting-started)
  * [Prerequisites](#prerequisites)
  * [Installation](#installation)
* [Usage](#usage)
* [Roadmap](#roadmap)
* [Contributing](#contributing)
* [License](#license)
* [Authors](#authors)
* [Acknowledgements](#acknowledgements)

## About The Project
The LuckNFTbot project is a comprehensive solution for anyone looking to quickly launch a feature-rich Telegram bot. Designed for engaging communication with an interested audience, the bot facilitates AirDrop NFTs, AirDrop Tokens, and includes a robust referral program. It is an ideal starting point for both hobbyists and professionals looking to delve into the world of Telegram bots.

## Built With
Developed using procedural PHP (version 7+), this project stands out for its simplicity and efficiency. The absence of third-party libraries means it's lightweight and can be deployed on any PHP and MySQL supported hosting. This straightforward approach also makes the bot highly customizable and accessible for developers at any skill level.

## Getting Started
To set up this project locally and get it running smoothly, follow these straightforward steps.

### Prerequisites
- Ensure your hosting environment supports PHP 7 and MySQL.
- Basic understanding of PHP and MySQL configuration is recommended.

### Installation

To get your Telegram bot up and running, follow these installation steps. The primary script for the bot is `tgbot.php`, which serves as the main executable.

1. **Configure User Data in `config.php`**:
   
   Edit the `config.php` file to include your specific user data. This configuration is crucial as it sets up the core parameters for your bot's operation. Here's a breakdown of what each line in the configuration file means:

   ```php
   // Main administrative user of the bot, typically the bot owner or developer.
   $admin = 00000; // Replace 00000 with your Telegram ChatID

   // These are the Telegram channel nicknames used for verifying user subscriptions.
   $channel_id1 = "@nickname"; // Replace "@nickname" with the actual nickname of your channel #1
   $channel_id2 = "@nickname"; // Replace "@nickname" with the nickname of your channel #2
   $channel_id3 = "@nickname"; // And so on for your channel #3

   // Wallet address for receiving payments.
   $recepientWallet = "XXXX"; // Replace "XXXX" with your TON Wallet address

   // Social media and external links associated with the bot.
   $twitterLNK = "https://twitter.com/nickname"; // Replace with your Twitter profile link
   $TGchannel1 = ""; // Link to an advertised Telegram channel
   $TGchannel2 = ""; // Another advertised Telegram channel
   $VKLNK = "https://vk.com/nickname"; // Replace with your VK profile link
   $MetaMaskLNK = "https://tegro.click/nickname"; // Replace with your MetaMask link

   // Bot API Token from Telegram.
   define('TOKEN', 'XXXXX'); // Replace 'XXXXX' with your actual Bot API Token


2. **Database Configuration**:
   - Update MySQL details in `global.php`.
   - Import structure from `database.sql`.

3. **Webhook Installation**:
   - Set up the webhook for `tgbot.php` at:
     [Set Webhook](https://api.telegram.org/botXXXXX/setWebhook?url=https://yourdomain/BotFolder/tgbot.php)

4. **Localize and Customize**:
   - Modify bot responses in `lang.php` as needed.

## Usage
Locate the bot in Telegram by searching `@YourBot`, and initiate interaction using the `/start` command. The bot's intuitive interface and responsive design make it easy for users to navigate and engage with its features.

## Roadmap
Discover what's next for LuckNFTbot by checking our [open issues](https://github.com/TGRTON/LuckNFTbot/issues) for a list of proposed features and ongoing improvements.

## Contributing
Your contributions are what drive the incredible growth and success of open source projects like this. We welcome and appreciate any contributions, big or small.

- **Suggestions and Improvements**: Feel free to [open an issue](https://github.com/TGRTON/LuckNFTbot/issues/new) to propose changes, or directly create a pull request with your updates to the README.md.
- **Best Practices**: Ensure your contributions are well-documented and error-free.
- **Respect the Process**: Adhere to our [Code Of Conduct](https://github.com/TGRTON/LuckNFTbot/blob/main/CODE_OF_CONDUCT.md) and follow our contribution guidelines for a smooth collaboration.

### Creating A Pull Request
1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License
For more information about the license, visit [License Commit Link](https://github.com/TGRTON/LuckNFTbot/commit/c0973569bab5932ae8b5d39c6a421cfc44c68671).

## Authors
- **Lana Cool** - Primary Developer - [Lana Cool's Profile](https://github.com/lana4cool/) - Specializing in Telegram Bots on PHP

## Acknowledgements
- Special thanks to [Lana](https://github.com/lana4cool/) for their invaluable contributions to this project.
