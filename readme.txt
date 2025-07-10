# 🧠 TLDR Plugin for WordPress

Generate concise, AI-powered summaries for your blog posts using the TLDR plugin and TurboLine API.

---

## 🔧 Features

- Automatically generate TLDR summaries using [TurboLine AI](https://platform.turboline.ai)
- Add summaries to your blog posts via shortcode
- Simple setup with API key integration
- Works seamlessly with Gutenberg and Classic Editor

---

## 🚀 Installation

1. Download the plugin `.zip` file.
2. In your WordPress admin dashboard, go to **Plugins → Add New → Upload Plugin**.
3. Upload the `.zip`, install, and activate the plugin.
4. Go to **Settings → TLDR Plugin** in the sidebar.
5. Follow the instructions below to get your API key.

---

## 🔐 How to Generate Your TLDR API Key

Follow these steps to get your API Key from TurboLine:

1. **Create an Account**  
   👉 [Sign Up at TurboLine](https://platform.turboline.ai/signup)

2. **Activate Your Account**  
   Check your email for the activation link and verify your account.

3. **Sign In to TurboLine Portal**  
   👉 [Sign In](https://platform.turboline.ai/signin)

4. **Subscribe to the TLDR Product**
   - Click on **Products** in the top right.
   - Select **TLDR**.

5. **Name Your Subscription**
   - Use something like _My WordPress Subscription_.
   - Click **Subscribe**.

6. **Access Your API Keys**
   - After subscribing, you will see your **Primary Key** and **Secondary Key**.

7. **Paste API Key into Plugin Settings**
   - In WordPress Admin, go to **Settings → TLDR Plugin**.
   - Paste your **Primary Key** into the API Key field.
   - Click **Save Changes**.

---

## 📝 Usage

Once your plugin is activated and the API key is configured:

- Add the shortcode `[turboline_tldr]` anywhere in your post or page.
- Upon publishing or updating your post, a TLDR summary will be generated and displayed.

---

## 🧩 Shortcode Reference

```shortcode
[turboline_tldr]