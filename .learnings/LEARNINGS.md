## [LRN-20260321-001] correction

**Logged**: 2026-03-22T01:41:00Z
**Priority**: high
**Status**: pending
**Area**: frontend

### Summary
I turned a chat workflow control into a giant dashboard block and wrecked the visual hierarchy.

### Details
User feedback made it clear the first revision of the chat commission-progress UI was fundamentally wrong. I moved the stage controls to the top as requested, but I implemented them as a large hero-style panel with duplicate explanatory text, oversized cards, and extra CTA rows. That crowded the thread header, pulled focus away from the actual conversation, and left the right rail still visually noisy. For chat UI, the correct pattern is compact, glanceable controls with minimal copy and strong hierarchy.

### Suggested Action
Replace the large top progress block with a compact segmented control or slim status row under the thread header. Keep labels short, remove explanatory filler copy, and reduce the right rail to truly secondary utilities.

### Metadata
- Source: user_feedback
- Related Files: resources/views/conversations/index.blade.php
- Tags: ui, hierarchy, chat, correction

---
