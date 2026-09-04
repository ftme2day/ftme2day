# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository status

This repository has no application source code, and no build or test tooling. There is nothing here to document in terms of build/lint/test commands or application architecture.

The only project-specific configuration is `.claude/settings.json`, which registers the `claude-code-skills` plugin marketplace (`alirezarezvani/claude-skills`) and enables its plugin bundles (engineering, engineering-advanced, product, marketing, ra-qm, pm, c-level, business-growth, finance skills, plus the skill-security-auditor, playwright-pro, self-improving-agent, and content-creator plugins) for anyone using Claude Code in this repo.

When code is added to this repository, update this file to reflect:
- Build, lint, and test commands (including how to run a single test)
- The high-level architecture and how the major pieces fit together
- Any project-specific conventions worth calling out
