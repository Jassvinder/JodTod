# Discussion: Add Member to Group - Flow Design

> **Status:** DECIDED (Phase 4.5 - Task 3)
> **Last Updated:** 2026-03-10

---

## Final Decision: Phone-Based Add (Current Phase)

For now, the primary method to add members to a group is **by phone number** (admin searches/enters phone number of a registered user). Other methods (invite link, contacts API) are kept as future options.

### Core Rules (Implemented):
1. **Phone verification is mandatory** for group features (create, join, add members)
2. **Members must be registered** in the app with a verified phone number
3. **Admin adds member by phone** → member gets in-app notification → confirms → added
4. **User cannot self-leave** a group — only admin can remove members
5. **Cannot remove member** if group has any expenses (to protect calculations)
6. Keep architecture open for future options (invite link, contacts API, SMS invites)

---

## Add Member Flow (Implemented)

### Step-by-step:
1. Admin taps "Add Member" on group detail page
2. Modal opens with phone number search input
3. Admin types phone number → system searches registered users
4. If user found (with verified phone): shows user name + avatar thumbnail
5. Admin taps "Add" → invitation sent to user
6. User receives in-app notification: "X invited you to group Y"
7. User confirms → added as member to group
8. If user not found → show message "No registered user with this phone number"

### UI on Group Detail Page:
```
[+ Add Member]  →  Opens modal:
  - Phone number input (with +91 prefix)
  - Search results: avatar + name + phone
  - "Add" button per result

[Share Invite Link]  →  Existing invite code/link modal (already built)
```

---

## Member Removal Rules

1. **Only admin can remove members** (user cannot self-leave)
2. **If group has expenses:** member CANNOT be removed (protect split calculations)
   - Show message: "Cannot remove member — group has expenses. Settle all expenses first."
3. **If group has no expenses:** admin can freely remove members
4. **Admin removal:** if admin is the only admin and removes themselves → transfer admin to oldest member (or delete group if last member)

---

## Options Kept for Future

### Option A: Invite Code / Link (Already Built)
- Invite code (8-char) already exists in groups table
- Share modal with copy code/link already built
- Join via link page already built
- **Future:** Add phone.verified check before joining via link too

### Option B: Contact List API (Future - Native App)
- Contact Picker API limited in PWA
- Full implementation when React Native / Flutter app is built
- Admin's contact list → highlight contacts who are on JodTod → select → invite

### Option C: SMS/WhatsApp Invite (Future)
- For non-registered users
- Requires SMS provider (MSG91, Twilio) — cost involved
- May need `group_invitations` table for pending invites:
  ```
  group_invitations: id, group_id, invited_by, phone, status (pending/accepted/expired), created_at
  ```

---

## Decision Log

| Date | Decision | Notes |
|------|----------|-------|
| 2026-03-10 | Phone-based add is primary method | Admin searches by phone, user must be registered |
| 2026-03-10 | Phone verify mandatory for groups | Middleware `phone.verified` added to group routes |
| 2026-03-10 | User cannot self-leave group | Only admin can remove members |
| 2026-03-10 | No member removal if expenses exist | Protect split calculations |
| 2026-03-10 | Profile avatar with crop | Cropperjs v2, shown in group member list |
| 2026-03-10 | Email verification UI added | Not mandatory yet, future use |
