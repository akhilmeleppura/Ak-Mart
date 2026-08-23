# 🚨 AKMART AI — INCIDENT RESPONSE PROTOCOL

**Document ID**: AKMART-DOC-AI-INCIDENT-009  
**Date**: August 2026  

---

## 1. SEVERITY TIERS & ESCALATION PATHS

- **SEV-1 (Critical)**: Unauthorized financial disclosure or automated data corruption $\rightarrow$ Immediate AI Gateway emergency killswitch disablement (`ai.copilot.enabled = false`) and engineering paging.
- **SEV-2 (High)**: Third-party LLM API provider degradation $\rightarrow$ Automatic failover to local deterministic offline engines.
- **SEV-3 (Medium)**: Individual prompt injection attempt flagged $\rightarrow$ Automated security log entry and IP velocity throttling.
