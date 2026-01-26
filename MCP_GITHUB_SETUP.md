# GitHub MCP Server Setup Guide

## Overview
The GitHub MCP server has been configured in `.cursorrules`. To use it, you need to set up a GitHub Personal Access Token (PAT).

## Steps to Set Up

### 1. Create a GitHub Personal Access Token

1. Go to GitHub Settings → Developer settings → Personal access tokens → Tokens (classic)
   - Direct link: https://github.com/settings/tokens

2. Click "Generate new token" → "Generate new token (classic)"

3. Configure the token:
   - **Note**: Give it a descriptive name (e.g., "Cursor MCP Server")
   - **Expiration**: Choose an appropriate expiration (90 days, 1 year, or no expiration)
   - **Scopes**: Select the permissions you need:
     - `repo` - Full control of private repositories (if working with private repos)
     - `read:org` - Read org and team membership (if working with organizations)
     - `read:packages` - Download packages from GitHub Package Registry
     - `read:user` - Read user profile data
     - `read:gpg_key` - Read GPG keys

4. Click "Generate token"

5. **IMPORTANT**: Copy the token immediately - you won't be able to see it again!

### 2. Set the Environment Variable

You have two options:

#### Option A: Set in your shell profile (Recommended)
Add this to your `~/.bashrc`, `~/.zshrc`, or `~/.config/fish/config.fish`:

```bash
export GITHUB_PERSONAL_ACCESS_TOKEN="your_token_here"
```

Then reload your shell:
```bash
source ~/.config/fish/config.fish  # For fish shell
# or
source ~/.bashrc  # For bash
# or
source ~/.zshrc   # For zsh
```

#### Option B: Set in Cursor's environment
If Cursor doesn't pick up shell environment variables, you may need to:
1. Set it in Cursor's settings/environment
2. Or directly edit `.cursorrules` and replace `${GITHUB_PERSONAL_ACCESS_TOKEN}` with your actual token (not recommended for security)

### 3. Restart Cursor

After setting the environment variable, restart Cursor to load the new configuration.

### 4. Verify the Setup

The GitHub MCP server should now be available. You can test it by:
- Listing repositories
- Reading file contents from GitHub
- Creating issues or pull requests
- And other GitHub operations

## Security Notes

- **Never commit your token** to version control
- Use the minimum required scopes
- Rotate tokens periodically
- If you accidentally commit a token, revoke it immediately on GitHub

## Troubleshooting

If the GitHub MCP server doesn't work:

1. Verify the token is set:
   ```bash
   echo $GITHUB_PERSONAL_ACCESS_TOKEN
   ```

2. Check Cursor's logs for MCP server errors

3. Ensure the token has the correct scopes for your operations

4. Try restarting Cursor after setting the environment variable

