# Connect this project to GitHub

The repository is initialized locally on branch `main` with an initial commit.

## 1. Create an empty repository on GitHub

1. Open [https://github.com/new](https://github.com/new)
2. **Repository name:** e.g. `cloudlablatam` or `cloudlablatam1`
3. **Visibility:** Private (recommended) or Public
4. Do **not** add README, `.gitignore`, or license (already in this project)
5. Click **Create repository**

## 2. Link and push from your PC

Replace `YOUR_USER` and `YOUR_REPO` with your GitHub username and repo name:

```powershell
cd C:\xampp\htdocs\cloudlablatam1

git remote add origin https://github.com/YOUR_USER/YOUR_REPO.git

git push -u origin main
```

**SSH** (if you use SSH keys):

```powershell
git remote add origin git@github.com:YOUR_USER/YOUR_REPO.git
git push -u origin main
```

## 3. Verify

- On GitHub, refresh the repo — you should see all project files except those in `.gitignore` (`.env`, `vendor/`, `node_modules/`, etc.).
- Never commit `.env` — it stays local and on the server only.

## Daily workflow

```powershell
git add .
git commit -m "Describe your change"
git push
```

## Optional: GitHub CLI

Install [GitHub CLI](https://cli.github.com/), then:

```powershell
gh auth login
gh repo create cloudlablatam --private --source=. --remote=origin --push
```
