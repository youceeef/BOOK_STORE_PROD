---
version: 1.0.0
name: BookStore Editorial System
description: A refined, architectural literary bookstore design system built for clarity, editorial rhythm, and zero AI-slop tells.
colors:
  ground: "#ffffff"
  ground-subtle: "#faf9f6"
  ground-dark: "#0a0a0f"
  surface-dark: "#12131a"
  border-light: "#e4e4e7"
  border-dark: "#27272a"
  ink-light: "#18181b"
  ink-dark: "#fafafa"
  muted-light: "#71717a"
  muted-dark: "#a1a1aa"
  signal: "#1e3a2f"
  signal-dark: "#c49a45"
  accent-hover: "#142820"
typography:
  display:
    fontFamily: "Playfair Display, Amiri, Georgia, serif"
    fontWeight: "700"
    letterSpacing: "-0.02em"
  body:
    fontFamily: "Inter, Noto Sans Arabic, system-ui, sans-serif"
    fontSize: "0.875rem"
    lineHeight: "1.5"
rounded:
  sm: "0.375rem"
  md: "0.5rem"
  lg: "0.75rem"
components:
  navbar:
    height: "4rem"
    backgroundLight: "#ffffff"
    backgroundDark: "#0e0f15"
    borderLight: "#e4e4e7"
    borderDark: "#1f2028"
  button-primary:
    backgroundLight: "#18181b"
    backgroundDark: "#c49a45"
    textLight: "#ffffff"
    textDark: "#0a0a0f"
    rounded: "0.5rem"
  card:
    borderLight: "#e4e4e7"
    borderDark: "#22232c"
    rounded: "0.75rem"
---

# BookStore Editorial Design System (Anti-AI Design Contract)

## Overview
This design system rejects generic AI-generated aesthetic tells (the purple/navy diagonal gradient, glowing neon shadows, balloon rounded-3xl corners, floating sticker badges over book artwork, and template chrome). 

Instead, it is grounded in the tactile vernacular of fine literary publishing, classic bookshops (Strand, Daunt Books, London Review Bookshop), and crisp architectural typesetting.

## Core Directives & Refused Tells
- **No Multi-Color Gradients (C1, C4, C6):** Solid, confident inks and grounds. No `from-gold-400 via-gold-500 to-gold-600` or `from-[#1a1a2e] to-[#252849]`.
- **No Balloon Radii or Diffuse Drop Shadows (K2):** Radius is restrained to `rounded-lg` (12px) and `rounded-md` (8px). Surfaces rely on crisp 1px hairline borders (`#e4e4e7` / `#27272a`), not muddy drop shadows.
- **No Artwork Occlusion (K4, K5):** Badges and pills must not float over book cover art. Category, author, title, and stock live cleanly in the metadata block below the artwork.
- **Architectural Navbar:** A clean 64px header with clear typography, integrated search, quiet language/theme controls, and zero visual clutter.
- **Discreet, Intentional Notifications:** Minimal toast alerts with high typographic contrast and no radioactive glows.
- **Full RTL & Dark Mode Fidelity:** Consistent tokens across English, French, and Arabic.
