# 🧪 AKMART AI — PHASE 7: FRAUD & RISK TEST REPORT

**Document ID**: AKMART-DOC-RISK-TESTS-007  
**QA Lead**: Principal QA & Test Automation Architect  
**Status**: 100% Passed  
**Date**: August 2026  

---

## 1. AUTOMATED TEST SUITE SUMMARY

| Test Case | Scenario Description | Expected Outcome | Result |
| :--- | :--- | :--- | :--- |
| **`test_normal_order_low_risk_assessment`** | Evaluates a normal order within customer's typical AOV range with 0 payment failures. | Score $< 30$, `risk_level = 'Low'`, `recommended_action = 'Auto-Approve'`. | ✅ **PASSED** |
| **`test_high_risk_order_with_aov_spike_and_payment_failures`** | Order is $4\times$ historical AOV with 2 failed payment transactions. | Score $\ge 55$, `risk_level` High/Medium, `recommended_action = 'Hold for Manual Verification'`. | ✅ **PASSED** |
| **`test_cod_risk_assessment`** | Customer with $\ge 50\%$ cancelled/RTO COD history. | Flags `risk_level = 'High'` and recommends `'Require Prepaid Payment'`. | ✅ **PASSED** |
| **`test_payment_anomaly_and_coupon_abuse_detection`** | Checks payment failure spikes and high velocity coupon redemptions. | Accurately identifies gateway failure rates and velocity flags. | ✅ **PASSED** |
