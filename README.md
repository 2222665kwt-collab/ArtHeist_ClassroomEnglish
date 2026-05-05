# Art Heist Mystery - Classroom English Web App

An interactive mystery roleplay activity for English language learners (ages 13-14) featuring three AI-powered suspect chatbots. Students interview suspects to solve an art theft case while practicing English conversation skills.

## Features

### For Students
- 🎭 **Three Interactive Chatbots**: Interview Margot Fleischer (Gallery Assistant), Victor Hale (Art Dealer), and Daniela Reyes (Security Guard)
- 💬 **Natural Conversations**: Each suspect has distinct personality, knowledge level, and motivations
- 🔍 **Mystery Solving**: Gather clues by asking strategic questions
- 📱 **Remote Access**: Students can access via web link from anywhere

### For Teachers
- 👨‍🏫 **Teacher Dashboard**: View all student conversations in real-time
- 📊 **Statistics**: Track conversation count, message volume, and active sessions
- 📥 **Download Chatlogs**: Export individual or all conversations as JSON files
- 🔍 **View Details**: Review full conversation transcripts with suspect and student messages
- 🗑️ **Manage Data**: Delete specific chatlogs or clear all conversations

## Setup Instructions

### Requirements
- PHP 7.4+ with enabled file operations
- Web server (Apache, Nginx, etc.)
- Modern web browser (Chrome, Firefox, Safari, Edge)

### Installation

1. **Clone or upload the repository** to your web server
   ```bash
   git clone https://github.com/2222665kwt-collab/ArtHeist_ClassroomEnglish.git
   cd ArtHeist_ClassroomEnglish
   ```

2. **Create chatlogs directory** (automatically created on first use):
   ```bash
   mkdir chatlogs
   chmod 755 chatlogs
   ```

3. **Set up on your web server**:
   - Copy all files to your web root (e.g., `/var/www/html/mystery/`)
   - Ensure PHP is configured to handle `.php` files
   - Test by accessing `http://your-domain.com/mystery/`

4. **Configure for remote access** (optional):
   - If using HTTPS (recommended): SSL certificate required
   - If using HTTP locally: works fine for classroom lab setups
   - Share the link to students via email or classroom platform

### File Structure

```
ArtHeist_ClassroomEnglish/
├── index.html                  # Student landing page
├── chat.html                   # Chat interface for interviewing suspects
├── teacher-dashboard.html      # Teacher dashboard for viewing logs
├── chat-api.php               # Backend API for suspect responses
├── save-chatlog.php           # Saves conversations to disk
├── get-chatlogs.php           # Lists all stored chatlogs
├── view-chatlog.php           # Views individual chatlog
├── download-chatlog.php       # Downloads individual chatlog
├── delete-chatlog.php         # Deletes individual chatlog
├── clear-all-chatlogs.php     # Clears all chatlogs
├── download-all-chatlogs.php  # Downloads all as ZIP
├── chatlogs/                  # Directory for storing conversation logs (auto-created)
└── README.md                  # This file
```

## How to Use

### For Students

1. **Access the app** via the provided web link
2. **Choose a suspect** from the three available options
3. **Ask questions** to investigate the art theft
4. **Look for inconsistencies** in their stories
5. **Interview all three suspects** to gather complete information

**Tips for Students:**
- Ask specific, detailed questions
- Take notes on their answers
- Compare stories between suspects
- Ask follow-up questions if answers seem vague

### For Teachers

1. **Access the dashboard** at `/teacher-dashboard.html`
2. **Monitor conversations** in real-time (refreshes every 5 seconds)
3. **View statistics**:
   - Total conversations
   - Total messages exchanged
   - Number of active student sessions
4. **Manage chatlogs**:
   - View full transcripts
   - Download individual or all conversations
   - Delete specific logs or clear everything

**Dashboard Features:**
- Filter chatlogs by suspect
- Search by time or date
- Download as JSON for analysis
- Bulk download all logs as ZIP file

## Suspect Profiles

### Margot Fleischer (Age 29)
**Role**: Gallery Assistant at Hartwell Gallery
- Works at the gallery for 18 months
- Knows alarm codes and has key access
- **Connected to the case**: She is the thief who let someone in

### Victor Hale (Age 54)
**Role**: Art Dealer, Owner of Hale & Sons Fine Art
- Tried to buy the painting three times
- Confident and formal in demeanor
- **Connected to the case**: Hiding a secret business meeting

### Daniela Reyes (Age 41)
**Role**: Night Security Guard
- 12 years of security experience
- Professional and honest
- **Connected to the case**: Was tricked into leaving the building during the theft

## The Crime

**What happened**: The painting "Girl in Blue" was stolen from the Hartwell Gallery on Thursday, April 17, between 9 PM and 11 PM.

**Student task**: Interview all three suspects, gather evidence, and piece together what really happened. Note: The app intentionally does not reveal guilt or innocence—students must discover the truth through careful questioning.

## Customization

### Modify Suspect Responses
Edit the `getRuleBasedResponses()` function in `chat-api.php` to:
- Add new keyword triggers
- Change response text
- Adjust difficulty level

### Change Character Details
Update suspect information in:
- `index.html` (suspect cards)
- `getCharacterPrompts()` in `chat-api.php` (character descriptions)

### Styling
Modify CSS in HTML files to match your school's branding:
- Colors: Change `#667eea` and `#764ba2` hex codes
- Fonts: Update `font-family` in style blocks
- Layout: Adjust grid and container widths

## Advanced Setup: Using AI Models

### Optional: Connect to Ollama (Local AI)

For more natural responses, install and run Ollama locally:

1. **Install Ollama**: Download from https://ollama.ai
2. **Run a model**: `ollama run mistral`
3. **The app will automatically detect** and use Ollama for responses
4. **Fallback**: If Ollama isn't available, uses rule-based responses

### Optional: Connect to OpenAI API (Paid)

To use OpenAI GPT-4 (requires API key and paid account):

1. **Get API key** from https://platform.openai.com
2. **Modify `chat-api.php`** to add OpenAI call:
   ```php
   function callOpenAI($messages, $apiKey) {
       // Implementation here
   }
   ```
3. **Note**: This requires server-side environment variable configuration

## Data Privacy & Security

- **Local storage**: All chatlogs are stored in the `chatlogs/` directory on your server
- **No cloud uploads**: Conversations never leave your server unless you download them
- **Teacher access**: Only accessible if they have direct URL access to `/teacher-dashboard.html`
- **Backup**: Regularly download and backup chatlogs

### Recommended Security Steps:
1. Use HTTPS (SSL certificate)
2. Password protect the `/teacher-dashboard.html` file via `.htaccess` or server config
3. Regularly backup the `chatlogs/` directory
4. Clear logs at end of school year

## Troubleshooting

### "No conversations yet"
- Check that students have actually sent messages
- Wait 5 seconds for dashboard to refresh
- Try refreshing the page manually

### Chat responses not appearing
- Check browser console for JavaScript errors (F12)
- Verify `chat-api.php` is accessible
- Ensure `chatlogs/` directory has write permissions (chmod 755)

### Download not working
- Verify PHP `zip` extension is installed
- Check server file permissions
- Try downloading individual chatlogs instead

### 404 errors
- Verify all files are uploaded to the correct directory
- Check that PHP is enabled on your server
- Ensure file names match exactly (case-sensitive on Linux)

## Language Learning Features

This app is designed specifically for English language learners (ESL/EFL):

✅ **Supports learning objectives**:
- Conversational practice with native-like responses
- Vocabulary in context (crime/investigation terminology)
- Question formation skills (interviews)
- Listening/reading comprehension
- Critical thinking and deduction

✅ **Appropriate language level**:
- Simple, clear vocabulary
- Short sentences (2-4 sentences max)
- Active listening rewards (if you ask good questions, you get better answers)
- Natural hesitations and filler words (realistic speech patterns)

✅ **Inclusive design**:
- No grammar correction (maintains student confidence)
- No punishment for mistakes
- Progressive complexity (questions lead to more details)
- Supports multiple learning paces

## License

This educational tool is designed for classroom use. Feel free to modify and adapt for your students' needs.

## Support

For issues or questions:
1. Check the **Troubleshooting** section above
2. Review the file structure and ensure all files are present
3. Check PHP error logs for server-side issues
4. Verify browser console for client-side errors

## Credits

Developed as an educational English language learning tool featuring:
- Three character archetypes common in mystery investigations
- Realistic alibis and motivations
- Designed for critical thinking practice

---

**Happy investigating!** 🔍🎨