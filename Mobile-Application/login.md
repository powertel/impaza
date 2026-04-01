# Impaza Mobile - Design Wireframes

## Splash Screen Wireframe

### Full Screen View

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│                                                             │
│                                                             │
│                  ┌───────────────────┐                     │
│                  │                   │                     │
│                  │   ┌─────────────┐ │                     │
│                  │   │             │ │                     │
│                  │   │   IMPAZA    │ │                     │
│                  │   │   LOGO      │ │                     │
│                  │   │             │ │                     │
│                  │   └─────────────┘ │                     │
│                  │                   │                     │
│                  └───────────────────┘                     │
│                                                             │
│                   IMPAZA MOBILE                            │
│               Fault Management System                      │
│                                                             │
│                                                             │
│                    ◉  ◉  ◉  Loading...                    │
│                                                             │
│                                                             │
│                                                             │
│                   Powered by Powertel                      │
│                                                             │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Component Breakdown

#### Logo Container
```
┌───────────────────┐
│                   │
│   ┌─────────────┐ │
│   │             │ │
│   │   IMPAZA    │ │
│   │   LOGO      │ │
│   │             │ │
│   └─────────────┘ │
│                   │
└───────────────────┘

Width: 200pt
Height: 200pt
Background: White with 20% opacity
Border Radius: 24pt
Shadow: Soft (0, 4pt, 12pt, rgba(0,0,0,0.1))
```

#### Loading Animation
```
Frame 1:    ◉ ○ ○
Frame 2:    ○ ◉ ○
Frame 3:    ○ ○ ◉
Frame 4:    ○ ◉ ○
Frame 5:    ◉ ○ ○

Duration: 1.5s per cycle
Dot Size: 8pt
Spacing: 4pt
Color: White
```

---

## Login Page Wireframe

### Full Screen View

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│                                                             │
│              ┌───────────────────┐                         │
│              │                   │                         │
│              │   ┌─────────────┐ │                         │
│              │   │             │ │                         │
│              │   │   IMPAZA    │ │                         │
│              │   │   LOGO      │ │                         │
│              │   │             │ │                         │
│              │   └─────────────┘ │                         │
│              │                   │                         │
│              └───────────────────┘                         │
│                                                             │
│          Welcome Back, Technician!                        │
│           Sign in to your account                         │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────────────────────────────────────────┐  │
│  │                                                     │  │
│  │  Email Address                                      │  │
│  │  ┌───────────────────────────────────────────────┐ │  │
│  │  │ 📧 john.doe@powertel.co.zw                   │ │  │
│  │  └───────────────────────────────────────────────┘ │  │
│  │                                                     │  │
│  │  Password                                           │  │
│  │  ┌───────────────────────────────────────────────┐ │  │
│  │  │ 🔒 ••••••••••••                           [👁]│ │  │
│  │  └───────────────────────────────────────────────┘ │  │
│  │                                                     │  │
│  │  ☐ Remember me                                     │  │
│  │                                                     │  │
│  │  ┌───────────────────────────────────────────────┐ │  │
│  │  │ 🔐 Sign In                                    │ │  │
│  │  └───────────────────────────────────────────────┘ │  │
│  │                                                     │  │
│  │  Forgot Password?                                  │  │
│  │                                                     │  │
│  └─────────────────────────────────────────────────────┘  │
│                                                             │
│         Don't have an account?                            │
│         Contact your administrator                        │
│                                                             │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Component Breakdown

#### Header Section
```
┌─────────────────────────────────────┐
│                                     │
│      ┌───────────────┐              │
│      │               │              │
│      │   [Logo]      │              │
│      │   (120x120)   │              │
│      │               │              │
│      └───────────────┘              │
│                                     │
│  Welcome Back, Technician!          │
│   Sign in to your account           │
│                                     │
└─────────────────────────────────────┘

Padding: 24pt horizontal, 32pt top
Logo Size: 120x120pt
Logo Background: Primary with 10% opacity
Border Radius: 20pt
```

#### Form Section
```
┌─────────────────────────────────────┐
│                                     │
│  Email Address                      │
│  ┌─────────────────────────────────┐│
│  │ 📧 john.doe@powertel.co.zw      ││
│  └─────────────────────────────────┘│
│                                     │
│  Password                           │
│  ┌─────────────────────────────────┐│
│  │ 🔒 ••••••••••••              [👁]││
│  └─────────────────────────────────┘│
│                                     │
│  ☐ Remember me                      │
│                                     │
│  ┌─────────────────────────────────┐│
│  │ 🔐 Sign In                      ││
│  └─────────────────────────────────┘│
│                                     │
│  Forgot Password?                   │
│                                     │
└─────────────────────────────────────┘

Padding: 24pt horizontal, 32pt vertical
Background: Surface
Border Radius: 20pt
Margin: 24pt horizontal
Border: 1pt, border color
```

#### Input Field Anatomy
```
Label: "Email Address"
┌─────────────────────────────────────┐
│ 📧 john.doe@powertel.co.zw      [✓] │
└─────────────────────────────────────┘
↑   ↑                                  ↑
Icon Left Padding                    Icon Right

Height: 48pt
Padding: 12pt horizontal, 12pt vertical
Border Radius: 12pt
Border: 1pt, border color
Icon Size: 16pt
Icon Spacing: 12pt from edges
```

#### Button States
```
Default State:
┌─────────────────────────────────────┐
│ 🔐 Sign In                          │
└─────────────────────────────────────┘
Background: Primary
Text Color: White

Pressed State:
┌─────────────────────────────────────┐
│ 🔐 Sign In                          │
└─────────────────────────────────────┘
Scale: 0.97
Opacity: 0.9

Loading State:
┌─────────────────────────────────────┐
│ ⟳ Signing in...                    │
└─────────────────────────────────────┘
Icon: Spinner
Text: "Signing in..."
Disabled: True

Success State:
┌─────────────────────────────────────┐
│ ✓ Sign In Successful                │
└─────────────────────────────────────┘
Background: Success green
Icon: Checkmark
Duration: 1 second

Error State:
┌─────────────────────────────────────┐
│ ✗ Sign In Failed                    │
└─────────────────────────────────────┘
Background: Error red
Icon: X mark
Duration: 2 seconds
```

#### Error Message Display
```
Email Input with Error:
┌─────────────────────────────────────┐
│ 📧 invalid-email                    │
└─────────────────────────────────────┘
⚠ Please enter a valid email address

Font Size: 12pt
Color: Error red
Icon: Exclamation circle (12pt)
Margin Top: 4pt
Animation: Fade in from top
```

---

## Login Page - Detailed Spacing

### Vertical Layout (Top to Bottom)

```
┌─────────────────────────────────────┐
│ Safe Area Top (Status Bar)          │ ← 0pt
├─────────────────────────────────────┤
│                                     │ ← 32pt (top padding)
│      ┌───────────────┐              │
│      │   [Logo]      │              │
│      │   (120x120)   │              │
│      │               │              │
│      └───────────────┘              │
│                                     │ ← 24pt (logo margin bottom)
│  Welcome Back, Technician!          │
│   Sign in to your account           │
│                                     │ ← 32pt (form margin top)
├─────────────────────────────────────┤
│  ┌─────────────────────────────────┐│
│  │                                 ││ ← 24pt (form padding)
│  │  Email Address                  ││
│  │  ┌───────────────────────────┐  ││ ← 8pt (label margin bottom)
│  │  │ 📧 john.doe@powertel...   │  ││
│  │  └───────────────────────────┘  ││ ← 20pt (field margin bottom)
│  │                                 ││
│  │  Password                       ││
│  │  ┌───────────────────────────┐  ││ ← 8pt (label margin bottom)
│  │  │ 🔒 ••••••••••••       [👁]│  ││
│  │  └───────────────────────────┘  ││ ← 16pt (field margin bottom)
│  │                                 ││
│  │  ☐ Remember me                  ││ ← 24pt (checkbox margin bottom)
│  │                                 ││
│  │  ┌───────────────────────────┐  ││
│  │  │ 🔐 Sign In                │  ││
│  │  └───────────────────────────┘  ││ ← 16pt (button margin bottom)
│  │                                 ││
│  │  Forgot Password?               ││ ← 32pt (link margin bottom)
│  │                                 ││ ← 24pt (form padding bottom)
│  └─────────────────────────────────┘│
│                                     │ ← 32pt (footer margin top)
│  Don't have an account?             │
│  Contact your administrator         │
│                                     │
│ Safe Area Bottom (Home Indicator)   │
└─────────────────────────────────────┘
```

---

## Responsive Design

### Portrait Mode (9:16)
```
Full layout as shown above
Width: Full screen
Keyboard: Pushes content up
Scrolling: Enabled if content exceeds screen
```

### Landscape Mode (16:9)
```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│  ┌────────────────────┐  ┌──────────────────────────┐ │
│  │                    │  │                          │ │
│  │   ┌────────────┐   │  │  Welcome Back,          │ │
│  │   │            │   │  │  Technician!            │ │
│  │   │   IMPAZA   │   │  │  Sign in to your        │ │
│  │   │   LOGO     │   │  │  account                │ │
│  │   │            │   │  │                          │ │
│  │   └────────────┘   │  │  ┌──────────────────┐   │ │
│  │                    │  │  │ 📧 john.doe@...  │   │ │
│  │                    │  │  └──────────────────┘   │ │
│  │                    │  │                          │ │
│  │                    │  │  ┌──────────────────┐   │ │
│  │                    │  │  │ 🔒 •••••••  [👁] │   │ │
│  │                    │  │  └──────────────────┘   │ │
│  │                    │  │                          │ │
│  │                    │  │  ☐ Remember me          │ │
│  │                    │  │                          │ │
│  │                    │  │  ┌──────────────────┐   │ │
│  │                    │  │  │ 🔐 Sign In       │   │ │
│  │                    │  │  └──────────────────┘   │ │
│  │                    │  │                          │ │
│  │                    │  │  Forgot Password?       │ │
│  │                    │  │                          │ │
│  └────────────────────┘  └──────────────────────────┘ │
│                                                         │
└─────────────────────────────────────────────────────────┘

Logo on left (25% width)
Form on right (75% width)
Horizontal layout
```

---

## Animation Sequences

### Splash Screen Animation

```
Timeline: 0ms → 2300ms

0ms - 300ms: Fade In
├─ Logo: opacity 0 → 1
├─ Title: opacity 0 → 1
├─ Subtitle: opacity 0 → 1
└─ Loading dots: opacity 0 → 1

300ms - 2000ms: Loading Loop
├─ Dot 1: ◉ ○ ○ → ○ ◉ ○ → ○ ○ ◉ → ○ ◉ ○ → ◉ ○ ○
├─ Duration: 1.5s per cycle
└─ Repeat: Until auth check completes

2000ms - 2300ms: Fade Out
├─ All elements: opacity 1 → 0
└─ Transition to next screen
```

### Login Page Animation

```
Page Load: 0ms - 300ms
├─ Header: Slide down + fade in
├─ Form: Slide up + fade in
└─ Easing: Ease out

Input Focus: 200ms
├─ Border color: Border → Primary
├─ Shadow: None → Glow
└─ Easing: Ease out

Button Press: 80ms
├─ Scale: 1 → 0.97
├─ Opacity: 1 → 0.9
└─ Easing: Ease in

Error Message: 300ms
├─ Animation: Fade in from top
├─ Shake: Subtle horizontal shake
└─ Duration: 300ms

Success Transition: 1000ms
├─ Button: Show checkmark
├─ Background: Primary → Success
├─ Duration: 1 second
└─ Transition: Fade to dashboard
```

---

## Color Specifications

### Splash Screen Colors

| Element | Light Mode | Dark Mode |
|---------|-----------|-----------|
| Background Gradient | `#0a7ea4` to `#086b8a` | `#0a7ea4` to `#053d52` |
| Logo Background | White (20% opacity) | White (20% opacity) |
| Text (Title) | White | White |
| Text (Subtitle) | White (80% opacity) | White (80% opacity) |
| Loading Dots | White | White |
| Footer Text | White (60% opacity) | White (60% opacity) |

### Login Page Colors

| Element | Light Mode | Dark Mode |
|---------|-----------|-----------|
| Background | `#ffffff` | `#151718` |
| Surface | `#f5f5f5` | `#1e2022` |
| Foreground | `#11181C` | `#ECEDEE` |
| Muted | `#687076` | `#9BA1A6` |
| Border | `#E5E7EB` | `#334155` |
| Primary | `#0a7ea4` | `#0a7ea4` |
| Error | `#EF4444` | `#F87171` |
| Success | `#22C55E` | `#4ADE80` |

---

## Implementation Notes

### Splash Screen
- Use `expo-splash-screen` for native splash
- Configure in `app.config.ts`
- Auto-dismiss after auth check
- No user interaction required
- Smooth transition to next screen

### Login Page
- Create `app/auth/login.tsx` screen
- Use `ScreenContainer` for safe area
- Implement form validation
- Handle keyboard appearance
- Add error recovery
- Support biometric auth (optional)

### Keyboard Handling
- iOS: Content shifts up when keyboard appears
- Android: Adjust layout for keyboard
- Use `KeyboardAvoidingView` or scroll
- Ensure button remains visible
- Dismiss keyboard on submit

### Accessibility
- All inputs have labels
- Error messages are descriptive
- Touch targets are 44x44pt minimum
- Color contrast is 4.5:1 or higher
- Screen reader support
- Keyboard navigation support

