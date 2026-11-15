<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CareOn' }} · CareOn</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            color: #172b4d;
            background: #f4f7fb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
        }

        a {
            color: #176b87;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px max(24px, calc((100% - 1080px) / 2));
            background: #fff;
            border-bottom: 1px solid #e1e8ef;
        }

        .brand {
            color: #123f5b;
            font-size: 21px;
            font-weight: 750;
            letter-spacing: -.04em;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .nav-links a {
            padding: 8px 0;
            color: #5c8495;
            font-size: 15px;
            font-weight: 550;
            text-decoration: none;
            transition: color .15s ease;
        }

        .nav-links a:hover {
            color: #123f5b;
        }

        .nav-links a:focus-visible {
            outline: 3px solid #176b8740;
            outline-offset: 2px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 18px;
            color: #5c8495;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: color .15s ease;
        }

        .back-link:hover {
            color: #123f5b;
        }

        .back-link:focus-visible {
            outline: 3px solid #176b8740;
            outline-offset: 3px;
        }

        .clinic-list-description {
            display: -webkit-box;
            overflow: hidden;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
            margin: 8px 0 0;
            color: #71849a;
            font-size: 14px;
            line-height: 1.5;
        }

        .clinic-directory {
            width: min(100%, 980px);
            margin: 0 auto;
        }

        .clinic-directory-header {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 24px;
            margin: 2px 0 26px;
        }

        .clinic-directory-eyebrow {
            display: inline-block;
            margin-bottom: 9px;
            color: #39869a;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .clinic-directory-header h1 {
            margin-bottom: 8px;
            font-size: clamp(30px, 4vw, 38px);
        }

        .clinic-directory-header p {
            margin: 0;
            font-size: 16px;
        }

        .clinic-directory-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .clinic-search-form {
            display: flex;
            width: min(100%, 430px);
            min-width: 280px;
            height: 46px;
            align-items: center;
            gap: 10px;
            padding: 4px;
            border: 1px solid #cbd9e3;
            border-radius: 10px;
            background: #fff;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .clinic-search-form:focus-within {
            border-color: #39869a;
            box-shadow: 0 0 0 3px #39869a20;
        }

        .clinic-search-icon {
            flex: 0 0 19px;
            width: 19px;
            height: 19px;
            margin-left: 9px;
            fill: none;
            stroke: #71849a;
            stroke-linecap: round;
            stroke-width: 1.8;
        }

        .clinic-search-form input {
            min-width: 0;
            height: 100%;
            padding: 0;
            border: 0;
            outline: 0;
            background: transparent;
            font-size: 14px;
        }

        .clinic-search-form input:focus {
            outline: 0;
        }

        .clinic-search-form input::placeholder {
            color: #8292a4;
        }

        .clinic-search-form button {
            flex: 0 0 auto;
            height: 36px;
            padding: 0 13px;
            border-radius: 7px;
            font-size: 14px;
        }

        .clinic-search-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: -10px 0 18px;
            color: #61748a;
            font-size: 14px;
        }

        .clinic-search-summary strong {
            color: #123f5b;
            font-weight: 650;
        }

        .clinic-search-summary a {
            font-weight: 650;
            text-decoration: none;
        }

        .clinic-search-summary a:hover {
            color: #123f5b;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            clip-path: inset(50%);
        }

        .clinic-count {
            padding: 7px 11px;
            border: 1px solid #dce8ed;
            border-radius: 999px;
            background: #fff;
            color: #5c7488;
            font-size: 13px;
            font-weight: 650;
            white-space: nowrap;
        }

        .clinic-directory-list {
            display: grid;
            gap: 16px;
        }

        .clinic-directory-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 22px;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        .clinic-directory-card:hover {
            border-color: #c8dfe5;
            box-shadow: 0 12px 30px #1b3a5718;
        }

        .clinic-directory-details {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 20px;
        }

        .clinic-directory-photo {
            flex: 0 0 104px;
            width: 104px;
            height: 104px;
            border: 1px solid #e4edf0;
            border-radius: 15px;
            background: #f1f6f8;
            object-fit: cover;
        }

        .clinic-directory-copy {
            min-width: 0;
        }

        .clinic-directory-copy h2 {
            margin: 0 0 7px;
            color: #123f5b;
            font-size: 19px;
        }

        .clinic-directory-address {
            overflow: hidden;
            margin: 0 0 5px;
            color: #61748a;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .clinic-directory-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            margin: 0;
            color: #8292a4;
            font-size: 13px;
        }

        .clinic-directory-action {
            flex: 0 0 auto;
            gap: 9px;
            white-space: nowrap;
        }

        .clinic-directory-action:focus-visible {
            outline: 3px solid #176b8740;
            outline-offset: 3px;
        }

        .clinic-directory-action span {
            font-size: 18px;
            line-height: 1;
            transition: transform .18s ease;
        }

        .clinic-directory-action:hover span {
            transform: translateX(3px);
        }

        .clinic-directory-empty {
            padding: 48px 24px;
            text-align: center;
        }

        .clinic-empty-mark {
            display: grid;
            width: 48px;
            height: 48px;
            margin: 0 auto 14px;
            place-items: center;
            border-radius: 15px;
            background: #eaf4f6;
            color: #39869a;
            font-size: 30px;
            font-weight: 500;
        }

        .clinic-empty-mark svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-width: 1.8;
        }

        .clinic-directory-empty h2 {
            margin-bottom: 5px;
            color: #123f5b;
        }

        .clinic-directory-empty p {
            margin: 0;
        }

        .patient-appointments-page {
            width: min(100%, 980px);
            margin: 0 auto;
        }

        .patient-appointments-header {
            margin-bottom: 30px;
        }

        .patient-appointment-section + .patient-appointment-section {
            margin-top: 38px;
        }

        .patient-section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 15px;
        }

        .patient-section-heading h2 {
            margin: 0 0 4px;
            color: #123f5b;
            font-size: 21px;
        }

        .patient-section-heading p {
            margin: 0;
            font-size: 14px;
        }

        .patient-appointment-list {
            display: grid;
            gap: 14px;
        }

        .doctor-appointments-filter-card {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
            padding: 19px 22px;
        }

        .doctor-appointments-filter-card h2 {
            margin: 0 0 4px;
            color: #123f5b;
            font-size: 18px;
        }

        .doctor-appointments-filter-card p {
            margin: 0;
            color: #71849a;
            font-size: 13px;
        }

        .doctor-appointments-filter-form {
            display: flex;
            align-items: flex-end;
            gap: 9px;
        }

        .doctor-appointments-filter-form label {
            margin: 0 0 6px;
            font-size: 12px;
        }

        .doctor-appointments-filter-form select {
            min-width: 160px;
            height: 41px;
        }

        .doctor-appointments-filter-form button {
            height: 41px;
            white-space: nowrap;
        }

        .doctor-appointments-filter-form > a {
            padding: 0 4px 11px;
            color: #39869a;
            font-size: 13px;
            font-weight: 650;
            text-decoration: none;
        }

        .doctor-appointments-filter-form > a:hover {
            color: #123f5b;
        }

        .patient-appointment-card {
            display: grid;
            grid-template-columns: 76px minmax(0, 1fr);
            align-items: start;
            gap: 20px;
            padding: 22px;
        }

        .doctor-appointment-card.appointment-status-card-completed,
        .doctor-appointment-card.appointment-status-card-cancelled {
            background: #f4f6f8;
        }

        .patient-appointment-date {
            display: flex;
            min-height: 92px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #d9e9ec;
            border-radius: 14px;
            background: linear-gradient(160deg, #edf7f7, #f7fbfc);
            color: #39869a;
        }

        .patient-appointment-date span {
            font-size: 12px;
            font-weight: 750;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .patient-appointment-date strong {
            color: #123f5b;
            font-size: 29px;
            line-height: 1.15;
        }

        .patient-appointment-date small {
            color: #71849a;
            font-size: 12px;
            font-weight: 650;
        }

        .patient-appointment-content {
            min-width: 0;
        }

        .patient-appointment-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .patient-appointment-title-row h3 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 19px;
        }

        .patient-appointment-time {
            margin: 0;
            color: #176b87;
            font-size: 14px;
            font-weight: 650;
        }

        .patient-appointment-time span {
            color: #8292a4;
            font-size: 13px;
            font-weight: 500;
        }

        .patient-appointment-status {
            flex: 0 0 auto;
            text-transform: capitalize;
        }

        .patient-appointment-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 22px;
            margin-top: 17px;
        }

        .patient-appointment-details p {
            min-width: 0;
            margin: 0;
            color: #61748a;
            font-size: 14px;
            line-height: 1.45;
        }

        .patient-appointment-details p > span:first-child {
            display: block;
            margin-bottom: 2px;
            color: #8292a4;
            font-size: 12px;
            font-weight: 650;
        }

        .patient-appointment-details strong {
            color: #344e68;
            font-weight: 650;
        }

        .patient-appointment-specialty {
            color: #71849a;
        }

        .doctor-appointment-details strong {
            overflow-wrap: anywhere;
        }

        .doctor-appointment-note {
            display: grid;
            gap: 4px;
            margin: 14px 0 0;
            padding: 11px 13px;
            border-radius: 9px;
            background: #f7f9fa;
            color: #526b80;
            font-size: 13px;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .doctor-appointment-note strong {
            color: #8292a4;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .doctor-appointment-cancel-note {
            background: #fff7f5;
        }

        .doctor-appointments-pagination {
            margin-top: 20px;
        }

        .doctor-appointments-empty {
            padding: 36px 24px;
        }

        .doctor-time-off-form-card {
            padding: clamp(22px, 4vw, 32px);
        }

        .doctor-time-off-section-heading {
            margin-bottom: 14px;
        }

        .doctor-time-off-section-heading h2 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 20px;
        }

        .doctor-time-off-section-heading p {
            max-width: 680px;
            margin: 0;
            color: #71849a;
            font-size: 14px;
            line-height: 1.5;
        }

        .doctor-time-off-fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 20px;
        }

        .doctor-time-off-fields > div {
            min-width: 0;
        }

        .doctor-time-off-fields label {
            margin: 14px 0 6px;
        }

        .doctor-time-off-help {
            margin: 6px 0 0;
            color: #8292a4;
            font-size: 12px;
        }

        .doctor-time-off-reason-field {
            grid-column: 1 / -1;
        }

        .doctor-time-off-form-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 22px;
            padding-top: 17px;
            border-top: 1px solid #edf1f4;
        }

        .doctor-time-off-form-actions p {
            margin: 0;
            color: #71849a;
            font-size: 13px;
        }

        .doctor-time-off-history {
            margin-top: 34px;
        }

        .doctor-time-off-empty {
            padding: 20px 22px;
        }

        .doctor-time-off-empty p {
            margin: 0;
        }

        .doctor-time-off-list {
            display: grid;
            gap: 11px;
        }

        .doctor-time-off-item {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 15px;
            padding: 18px;
        }

        .doctor-time-off-date-icon {
            display: grid;
            flex: 0 0 44px;
            width: 44px;
            height: 44px;
            place-items: center;
            border-radius: 13px;
            background: #e9f4f5;
            color: #176b87;
        }

        .doctor-time-off-date-icon svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.7;
        }

        .doctor-time-off-item-content {
            min-width: 0;
            flex: 1 1 auto;
        }

        .doctor-time-off-item-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .doctor-time-off-item-heading > div {
            min-width: 0;
        }

        .doctor-time-off-item-heading h3 {
            margin: 0 0 4px;
            color: #123f5b;
            font-size: 15px;
        }

        .doctor-time-off-item-heading p {
            margin: 0;
            color: #425c73;
            font-size: 13px;
            font-variant-numeric: tabular-nums;
            overflow-wrap: anywhere;
        }

        .doctor-time-off-item-heading .doctor-time-off-time-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 7px 9px;
        }

        .doctor-time-off-status {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .doctor-time-off-status.is-upcoming {
            background: #e8f5ee;
            color: #26754f;
        }

        .doctor-time-off-status.is-past {
            background: #edf1f4;
            color: #71849a;
        }

        .doctor-time-off-item-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 5px 14px;
            margin-top: 7px;
            color: #8292a4;
            font-size: 12px;
        }

        .doctor-time-off-item-meta span + span::before {
            content: "·";
            margin-right: 7px;
            color: #b0bdc7;
        }

        .doctor-time-off-remove-form {
            flex: 0 0 auto;
        }

        .doctor-time-off-remove-form button {
            padding: 8px 11px;
            font-size: 12px;
        }

        .patient-appointment-footer {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-top: 18px;
            padding-top: 15px;
            border-top: 1px solid #edf1f4;
        }

        .patient-cancel-details summary {
            color: #a53232;
            font-size: 14px;
            font-weight: 650;
            cursor: pointer;
            list-style-position: inside;
        }

        .patient-cancel-details form {
            width: min(100%, 420px);
            margin-top: 10px;
        }

        .patient-cancel-details label {
            margin-top: 10px;
        }

        .patient-cancel-button {
            margin-top: 10px;
            background: #a53232;
        }

        .patient-cancel-button:hover {
            background: #842828;
        }

        .patient-cancellation-note {
            margin: 0;
            color: #71849a;
            font-size: 13px;
        }

        .patient-appointments-empty {
            padding: 36px 24px;
            text-align: center;
        }

        .patient-appointments-empty h2 {
            margin-bottom: 5px;
            color: #123f5b;
        }

        .patient-appointments-empty p {
            margin: 0 0 18px;
        }

        .patient-history-section .patient-section-heading {
            margin-bottom: 15px;
        }

        .patient-history-card {
            padding: 10px 18px;
        }

        .patient-history-card .table-wrap {
            margin-top: 0;
        }

        .patient-history-card tr:last-child td {
            border-bottom: 0;
        }

        .patient-history-empty {
            padding: 20px 22px;
        }

        .patient-history-empty p {
            margin: 0;
        }

        .patient-profile-page {
            width: min(100%, 980px);
            margin: 0 auto;
        }

        .clinic-profile-page {
            width: min(100%, 980px);
            margin: 0 auto;
        }

        .clinic-dashboard-page {
            width: min(100%, 1080px);
            margin: 0 auto;
        }

        .clinic-dashboard-welcome {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 28px;
            padding: clamp(26px, 4vw, 40px);
            border: 1px solid #d9e8ed;
            border-radius: 20px;
            background: linear-gradient(120deg, #fff 0%, #f5fbfb 100%);
            box-shadow: 0 12px 30px #123f5b0a;
        }

        .clinic-dashboard-welcome-content {
            min-width: 0;
        }

        .clinic-dashboard-welcome h1 {
            margin: 12px 0 8px;
            color: #123f5b;
            font-size: clamp(28px, 4vw, 38px);
        }

        .clinic-dashboard-welcome-content > p {
            max-width: 620px;
            margin: 0;
            color: #61748a;
            font-size: 16px;
        }

        .clinic-dashboard-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 18px;
            margin-top: 22px;
        }

        .clinic-dashboard-meta span {
            display: inline-flex;
            min-width: 0;
            align-items: center;
            gap: 7px;
            color: #61748a;
            font-size: 13px;
        }

        .clinic-dashboard-meta svg {
            flex: 0 0 17px;
            width: 17px;
            height: 17px;
            fill: none;
            stroke: #39869a;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.7;
        }

        .clinic-dashboard-profile-link {
            flex: 0 0 auto;
        }

        .clinic-dashboard-workspace {
            margin-top: 34px;
        }

        .clinic-dashboard-section-heading {
            margin-bottom: 16px;
        }

        .clinic-dashboard-section-heading h2 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 22px;
        }

        .clinic-dashboard-section-heading p {
            margin: 0;
            color: #71849a;
            font-size: 14px;
        }

        .clinic-dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .clinic-dashboard-card {
            display: flex;
            min-width: 0;
            min-height: 126px;
            align-items: center;
            gap: 16px;
            padding: 22px;
            border: 1px solid #dce7ed;
            border-radius: 15px;
            background: #fff;
            color: inherit;
            text-decoration: none;
            transition: border-color .16s ease, box-shadow .16s ease, color .16s ease;
        }

        .clinic-dashboard-card:hover {
            border-color: #9cc8d2;
            box-shadow: 0 8px 20px #123f5b0c;
            color: #123f5b;
        }

        .clinic-dashboard-card:focus-visible {
            outline: 3px solid #176b8740;
            outline-offset: 3px;
        }

        .clinic-dashboard-card-icon {
            display: grid;
            flex: 0 0 46px;
            width: 46px;
            height: 46px;
            place-items: center;
            border-radius: 13px;
            background: #e9f4f5;
            color: #176b87;
        }

        .clinic-dashboard-card-icon svg {
            width: 23px;
            height: 23px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.7;
        }

        .clinic-dashboard-card-copy {
            display: grid;
            min-width: 0;
            gap: 5px;
        }

        .clinic-dashboard-card-copy strong {
            color: #123f5b;
            font-size: 16px;
        }

        .clinic-dashboard-card-copy > span {
            color: #71849a;
            font-size: 14px;
            line-height: 1.45;
        }

        .clinic-dashboard-card-arrow {
            flex: 0 0 auto;
            margin-left: auto;
            color: #39869a;
            font-size: 21px;
            line-height: 1;
        }

        .clinic-appointments-page {
            width: min(100%, 980px);
            margin: 0 auto;
        }

        .clinic-appointments-header {
            align-items: center;
        }

        .clinic-appointments-clock {
            display: grid;
            flex: 0 0 auto;
            min-width: 210px;
            gap: 4px;
            padding: 13px 16px;
            border: 1px solid #d9e8ed;
            border-radius: 13px;
            background: #fff;
            text-align: right;
        }

        .clinic-appointments-clock time {
            color: #123f5b;
            font-size: 23px;
            font-weight: 750;
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
        }

        .clinic-appointments-clock > span:last-child {
            color: #71849a;
            font-size: 12px;
        }

        .clinic-appointments-filter-card {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 16px;
            padding: 20px 22px;
        }

        .clinic-appointments-filter-copy h2 {
            margin: 0 0 4px;
            color: #123f5b;
            font-size: 18px;
        }

        .clinic-appointments-filter-copy p {
            margin: 0;
            color: #71849a;
            font-size: 13px;
        }

        .clinic-appointments-filter-form {
            display: flex;
            align-items: flex-end;
            gap: 9px;
        }

        .clinic-appointments-filter-form label {
            margin: 0 0 6px;
            font-size: 12px;
        }

        .clinic-appointments-filter-form select {
            min-width: 160px;
            height: 41px;
        }

        .clinic-appointments-filter-form button {
            height: 41px;
            white-space: nowrap;
        }

        .clinic-appointments-clear-filter {
            padding: 0 4px 11px;
            color: #39869a;
            font-size: 13px;
            font-weight: 650;
            text-decoration: none;
        }

        .clinic-appointments-clear-filter:hover {
            color: #123f5b;
        }

        .clinic-appointments-list {
            display: grid;
            gap: 13px;
        }

        .clinic-appointment-card {
            padding: 22px;
        }

        .clinic-appointment-card[data-status="completed"],
        .clinic-appointment-card[data-status="cancelled"] {
            background: #f4f6f8;
        }

        .clinic-appointment-card-main {
            display: grid;
            grid-template-columns: 66px minmax(0, 1fr);
            gap: 18px;
        }

        .clinic-appointment-date-block {
            display: flex;
            min-height: 88px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #d9e9ec;
            border-radius: 13px;
            background: linear-gradient(160deg, #edf7f7, #f8fbfc);
        }

        .clinic-appointment-date-block span {
            color: #39869a;
            font-size: 11px;
            font-weight: 750;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .clinic-appointment-date-block strong {
            color: #123f5b;
            font-size: 27px;
            line-height: 1.15;
        }

        .clinic-appointment-date-block small {
            color: #71849a;
            font-size: 11px;
            font-weight: 650;
        }

        .clinic-appointment-details {
            min-width: 0;
        }

        .clinic-appointment-heading-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }

        .clinic-appointment-heading-row h2 {
            margin: 3px 0 0;
            color: #123f5b;
            font-size: 18px;
        }

        .clinic-appointment-time {
            margin: 0;
            color: #176b87;
            font-size: 13px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        .clinic-appointment-time-row {
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 4px 7px;
            margin: 0;
        }

        .clinic-appointment-time-row [data-time-status]::before {
            content: "|";
            margin-right: 7px;
            color: #a1afbc;
            font-weight: 400;
        }

        .clinic-appointment-live-status {
            color: #176b87;
            font-size: 12px;
            font-weight: 650;
        }

        .clinic-appointment-live-status[data-live-state="current"] {
            color: #26754f;
        }

        .clinic-appointment-live-status[data-live-state="past"] {
            color: #71849a;
        }

        .clinic-appointment-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px 18px;
            margin-top: 16px;
        }

        .clinic-appointment-meta > span {
            display: grid;
            min-width: 0;
            gap: 3px;
            color: #425c73;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .clinic-appointment-meta strong,
        .clinic-appointment-note strong {
            color: #8292a4;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .clinic-appointment-meta a {
            color: inherit;
            text-decoration: none;
        }

        .clinic-appointment-meta a:hover {
            color: #176b87;
            text-decoration: underline;
        }

        .clinic-appointment-note {
            display: grid;
            gap: 4px;
            margin: 14px 0 0;
            padding: 11px 13px;
            border-radius: 9px;
            background: #f7f9fa;
            color: #526b80;
            font-size: 13px;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .clinic-appointment-cancel-note {
            background: #fff7f5;
        }

        .clinic-appointment-actions {
            display: flex;
            align-items: flex-start;
            gap: 18px;
            margin-top: 18px;
            padding-top: 15px;
            border-top: 1px solid #edf1f4;
        }

        .clinic-appointment-cancel {
            position: relative;
        }

        .clinic-appointment-cancel summary {
            padding: 9px 0;
            color: #a53232;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            list-style-position: inside;
        }

        .clinic-appointment-cancel form {
            width: min(100%, 420px);
            margin-top: 8px;
        }

        .clinic-appointment-cancel label {
            margin: 0 0 6px;
        }

        .clinic-appointment-cancel button {
            margin-top: 10px;
            background: #a53232;
        }

        .clinic-appointment-cancel button:hover {
            background: #842828;
        }

        .clinic-appointments-empty {
            padding: 42px 24px;
            text-align: center;
        }

        .clinic-appointments-empty > span {
            display: grid;
            width: 44px;
            height: 44px;
            margin: 0 auto 13px;
            place-items: center;
            border-radius: 14px;
            background: #e9f4f5;
            color: #39869a;
            font-size: 24px;
        }

        .clinic-appointments-empty h2 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 19px;
        }

        .clinic-appointments-empty p {
            margin: 0 0 16px;
            color: #71849a;
            font-size: 14px;
        }

        .clinic-appointments-pagination {
            margin-top: 20px;
        }

        .clinic-workspace-page {
            width: min(100%, 980px);
            margin: 0 auto;
        }

        .clinic-workspace-header {
            margin-bottom: 24px;
        }

        .clinic-workspace-card {
            padding: clamp(22px, 4vw, 32px);
        }

        .clinic-workspace-section {
            margin-top: 34px;
        }

        .clinic-workspace-section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .clinic-workspace-section-heading h2,
        .clinic-workspace-section-heading h3 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 20px;
        }

        .clinic-workspace-section-heading h3 {
            font-size: 17px;
        }

        .clinic-workspace-section-heading p {
            max-width: 680px;
            margin: 0;
            color: #71849a;
            font-size: 14px;
            line-height: 1.5;
        }

        .clinic-workspace-count {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: #edf4f6;
            color: #526b80;
            font-size: 12px;
            font-weight: 700;
        }

        .clinic-workspace-empty {
            padding: 32px 22px;
            text-align: center;
        }

        .clinic-workspace-empty > span {
            display: grid;
            width: 44px;
            height: 44px;
            margin: 0 auto 12px;
            place-items: center;
            border-radius: 14px;
            background: #e9f4f5;
            color: #39869a;
            font-size: 25px;
        }

        .clinic-workspace-empty > span svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.7;
        }

        .clinic-workspace-empty h2,
        .clinic-workspace-empty h3 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 18px;
        }

        .clinic-workspace-empty p {
            max-width: 560px;
            margin: 0 auto;
            color: #71849a;
            font-size: 14px;
            line-height: 1.5;
        }

        .clinic-service-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .clinic-service-option {
            position: relative;
            display: flex;
            min-width: 0;
            align-items: flex-start;
            gap: 13px;
            margin: 0;
            padding: 17px;
            border: 1px solid #dce7ed;
            border-radius: 13px;
            background: #fff;
            cursor: pointer;
            font-weight: 400;
            transition: border-color .15s ease, background-color .15s ease;
        }

        .clinic-service-option:has(input:checked) {
            border-color: #70aebc;
            background: #f3fafb;
        }

        .clinic-service-option:focus-within {
            outline: 3px solid #176b8740;
            outline-offset: 2px;
        }

        .clinic-service-option input[type="checkbox"] {
            flex: 0 0 18px;
            width: 18px;
            height: 18px;
            margin: 2px 0 0;
            accent-color: #176b87;
        }

        .clinic-service-option-content {
            display: grid;
            min-width: 0;
            gap: 5px;
        }

        .clinic-service-option-content > strong {
            color: #123f5b;
            font-size: 15px;
        }

        .clinic-service-specialty {
            color: #39869a;
            font-size: 12px;
            font-weight: 650;
        }

        .clinic-service-description {
            display: -webkit-box;
            overflow: hidden;
            color: #71849a;
            font-size: 13px;
            line-height: 1.45;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .clinic-service-check {
            display: none;
            flex: 0 0 22px;
            width: 22px;
            height: 22px;
            margin-left: auto;
            place-items: center;
            border-radius: 50%;
            background: #176b87;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
        }

        .clinic-service-option:has(input:checked) .clinic-service-check {
            display: grid;
        }

        .clinic-workspace-form-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #edf1f4;
        }

        .clinic-workspace-form-actions p {
            margin: 0;
            color: #71849a;
            font-size: 13px;
        }

        .clinic-workspace-action-buttons {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 9px;
        }

        .clinic-schedule-guidance {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            margin: -6px 0 20px;
            padding: 14px 16px;
            border: 1px solid #d9e8ed;
            border-radius: 12px;
            background: #f5fafb;
        }

        .clinic-schedule-guidance > span {
            display: grid;
            flex: 0 0 22px;
            width: 22px;
            height: 22px;
            place-items: center;
            border-radius: 50%;
            background: #e2f0f2;
            color: #176b87;
            font-size: 13px;
            font-weight: 750;
        }

        .clinic-schedule-guidance p {
            margin: 1px 0 0;
            color: #526b80;
            font-size: 13px;
            line-height: 1.5;
        }

        .clinic-doctor-schedule-card {
            margin-top: 16px;
            padding: 22px;
        }

        .clinic-doctor-schedule-header {
            display: flex;
            align-items: center;
            gap: 13px;
            padding-bottom: 17px;
            border-bottom: 1px solid #edf1f4;
        }

        .clinic-doctor-schedule-avatar {
            display: grid;
            flex: 0 0 44px;
            width: 44px;
            height: 44px;
            place-items: center;
            border-radius: 14px;
            background: #e9f4f5;
            color: #176b87;
            font-size: 14px;
            font-weight: 750;
        }

        .clinic-doctor-schedule-avatar svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.7;
        }

        .clinic-doctor-schedule-header h2 {
            margin: 0 0 3px;
            color: #123f5b;
            font-size: 18px;
        }

        .clinic-doctor-schedule-header p {
            margin: 0;
            color: #71849a;
            font-size: 13px;
        }

        .clinic-doctor-schedule-header .clinic-workspace-count {
            margin-left: auto;
        }

        .clinic-schedule-empty {
            margin-top: 16px;
            padding: 16px;
            border-radius: 10px;
            background: #f8fafb;
            color: #71849a;
            font-size: 13px;
        }

        .clinic-schedule-empty p {
            margin: 0;
        }

        .clinic-schedule-list {
            display: grid;
            gap: 9px;
            margin-top: 16px;
        }

        .clinic-schedule-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 15px;
            border: 1px solid #e3ebef;
            border-radius: 11px;
            background: #fff;
        }

        .clinic-schedule-day {
            display: grid;
            gap: 3px;
        }

        .clinic-schedule-day > span {
            color: #8292a4;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .clinic-schedule-day strong {
            color: #123f5b;
            font-size: 16px;
            font-variant-numeric: tabular-nums;
        }

        .clinic-schedule-day small {
            color: #71849a;
            font-size: 12px;
        }

        .clinic-schedule-actions {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 14px;
        }

        .clinic-schedule-edit summary {
            color: #176b87;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .clinic-schedule-edit form {
            width: min(100%, 600px);
            margin-top: 14px;
        }

        .clinic-schedule-form-grid,
        .clinic-schedule-add-form {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            align-items: end;
        }

        .clinic-schedule-form-grid label,
        .clinic-schedule-add-form label {
            margin: 0 0 6px;
            font-size: 12px;
        }

        .clinic-schedule-edit button {
            margin-top: 13px;
        }

        .clinic-schedule-remove {
            padding: 8px 11px;
            font-size: 12px;
        }

        .clinic-schedule-add-section {
            margin-top: 22px;
            padding: 20px;
            border: 1px solid #e3ebef;
            border-radius: 12px;
            background: #f9fbfc;
        }

        .clinic-schedule-add-section .clinic-workspace-section-heading {
            margin-bottom: 14px;
        }

        .clinic-schedule-add-form {
            grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
        }

        .clinic-schedule-submit-wrap button {
            white-space: nowrap;
        }

        .clinic-no-doctors-empty {
            padding: 42px 24px;
        }

        .clinic-no-doctors-action {
            margin-top: 17px;
        }

        .clinic-doctor-invite-card {
            padding: clamp(22px, 4vw, 30px);
        }

        .clinic-doctor-invite-icon {
            display: grid;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            place-items: center;
            border-radius: 13px;
            background: #e9f4f5;
            color: #176b87;
            font-size: 27px;
            font-weight: 400;
        }

        .clinic-doctor-invite-form {
            display: flex;
            max-width: 680px;
            align-items: flex-end;
            gap: 10px;
        }

        .clinic-doctor-invite-form > div {
            flex: 1 1 auto;
            min-width: 0;
        }

        .clinic-doctor-invite-form label {
            margin: 0 0 6px;
        }

        .clinic-doctor-invite-form button {
            flex: 0 0 auto;
        }

        .clinic-doctor-team-list {
            display: grid;
            gap: 11px;
        }

        .clinic-doctor-team-card {
            display: grid;
            grid-template-columns: auto minmax(190px, 1.4fr) repeat(3, minmax(120px, 1fr));
            align-items: center;
            gap: 17px;
            padding: 18px;
        }

        .clinic-doctor-team-identity {
            display: grid;
            min-width: 0;
            gap: 4px;
        }

        .clinic-doctor-team-identity h3 {
            margin: 0;
            color: #123f5b;
            font-size: 15px;
        }

        .clinic-doctor-team-identity a {
            color: #71849a;
            font-size: 12px;
            overflow-wrap: anywhere;
            text-decoration: none;
        }

        .clinic-doctor-team-identity a:hover {
            color: #176b87;
            text-decoration: underline;
        }

        .clinic-doctor-team-detail {
            display: grid;
            justify-items: start;
            gap: 5px;
        }

        .clinic-doctor-team-detail > span:first-child {
            color: #8292a4;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .clinic-doctor-team-detail strong {
            color: #425c73;
            font-size: 12px;
        }

        .clinic-doctor-team-detail .clinic-doctor-verified {
            color: #26754f;
        }

        .clinic-doctor-team-detail .clinic-doctor-unverified {
            color: #a56819;
        }

        .clinic-invitation-list {
            padding: 4px 18px;
        }

        .clinic-invitation-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 15px 2px;
        }

        .clinic-invitation-row + .clinic-invitation-row {
            border-top: 1px solid #edf1f4;
        }

        .clinic-invitation-email {
            display: grid;
            min-width: 0;
            gap: 4px;
        }

        .clinic-invitation-email strong {
            color: #123f5b;
            font-size: 14px;
            overflow-wrap: anywhere;
        }

        .clinic-invitation-email > span {
            color: #8292a4;
            font-size: 12px;
        }

        .clinic-invitation-status {
            flex: 0 0 auto;
            padding: 5px 9px;
            border-radius: 999px;
            background: #edf4f6;
            color: #526b80;
            font-size: 11px;
            font-weight: 700;
        }

        .clinic-invitation-status-accepted {
            background: #e8f5ee;
            color: #26754f;
        }

        .clinic-invitation-status-pending {
            background: #fff4df;
            color: #94621a;
        }

        .clinic-invitation-status-declined,
        .clinic-invitation-status-expired,
        .clinic-invitation-status-replaced {
            background: #f5eeee;
            color: #8b4f4f;
        }

        .clinic-invitation-empty {
            padding: 20px;
        }

        .doctor-dashboard-page {
            width: min(100%, 1080px);
            margin: 0 auto;
        }

        .doctor-dashboard-welcome {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
            padding: clamp(26px, 4vw, 40px);
            border: 1px solid #d9e8ed;
            border-radius: 20px;
            background: linear-gradient(120deg, #fff 0%, #f5fbfb 100%);
            box-shadow: 0 12px 30px #123f5b0a;
        }

        .doctor-dashboard-welcome-copy {
            min-width: 0;
        }

        .doctor-dashboard-welcome h1 {
            margin: 12px 0 8px;
            color: #123f5b;
            font-size: clamp(28px, 4vw, 38px);
        }

        .doctor-dashboard-welcome-copy > p {
            max-width: 620px;
            margin: 0;
            color: #61748a;
            font-size: 16px;
        }

        .doctor-dashboard-credentials {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 28px;
            margin-top: 22px;
        }

        .doctor-dashboard-credentials > div {
            display: grid;
            gap: 4px;
        }

        .doctor-dashboard-credentials span {
            color: #8292a4;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .doctor-dashboard-credentials strong {
            color: #344e68;
            font-size: 14px;
            overflow-wrap: anywhere;
        }

        .doctor-verification-card {
            display: flex;
            flex: 0 0 min(300px, 35%);
            align-items: flex-start;
            gap: 12px;
            padding: 17px;
            border: 1px solid #dce7ed;
            border-radius: 14px;
            background: #fff;
        }

        .doctor-verification-card.is-verified {
            border-color: #c9deef;
            background: #f3f9fe;
        }

        .doctor-verification-card.is-pending {
            border-color: #f0dfbd;
            background: #fffaf0;
        }

        .doctor-verification-icon {
            display: grid;
            flex: 0 0 36px;
            width: 36px;
            height: 36px;
            place-items: center;
            border-radius: 11px;
            background: #fff;
            color: #26754f;
        }

        .is-pending .doctor-verification-icon {
            color: #a56819;
        }

        .doctor-verification-icon svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.8;
        }

        .doctor-verification-icon img {
            display: block;
            width: 22px;
            height: 22px;
            object-fit: contain;
        }

        .doctor-verification-card.is-verified .doctor-verification-icon {
            background: #e5f2fc;
            color: #1d75b8;
        }

        .doctor-verification-card strong {
            color: #123f5b;
            font-size: 14px;
        }

        .doctor-verification-card p {
            margin: 5px 0 0;
            color: #71849a;
            font-size: 12px;
            line-height: 1.45;
        }

        .doctor-dashboard-section {
            margin-top: 34px;
        }

        .doctor-dashboard-section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .doctor-dashboard-section-heading h2 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 22px;
        }

        .doctor-dashboard-section-heading p {
            margin: 0;
            color: #71849a;
            font-size: 14px;
        }

        .doctor-dashboard-actions-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .doctor-dashboard-action-card {
            display: flex;
            min-width: 0;
            min-height: 124px;
            align-items: center;
            gap: 16px;
            padding: 22px;
            border: 1px solid #dce7ed;
            border-radius: 15px;
            background: #fff;
            color: inherit;
            text-decoration: none;
            transition: border-color .16s ease, box-shadow .16s ease, color .16s ease;
        }

        .doctor-dashboard-action-card:hover {
            border-color: #9cc8d2;
            box-shadow: 0 8px 20px #123f5b0c;
            color: #123f5b;
        }

        .doctor-dashboard-action-card:focus-visible {
            outline: 3px solid #176b8740;
            outline-offset: 3px;
        }

        .doctor-dashboard-action-icon,
        .doctor-dashboard-clinic-icon,
        .doctor-dashboard-empty-icon {
            display: grid;
            flex: 0 0 46px;
            width: 46px;
            height: 46px;
            place-items: center;
            border-radius: 13px;
            background: #e9f4f5;
            color: #176b87;
        }

        .doctor-dashboard-action-icon svg,
        .doctor-dashboard-clinic-icon svg,
        .doctor-dashboard-empty-icon svg {
            width: 23px;
            height: 23px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.7;
        }

        .doctor-dashboard-action-copy {
            display: grid;
            min-width: 0;
            gap: 5px;
        }

        .doctor-dashboard-action-copy strong {
            color: #123f5b;
            font-size: 16px;
        }

        .doctor-dashboard-action-copy > span {
            color: #71849a;
            font-size: 14px;
            line-height: 1.45;
        }

        .doctor-dashboard-action-arrow {
            flex: 0 0 auto;
            margin-left: auto;
            color: #39869a;
            font-size: 21px;
            line-height: 1;
        }

        .doctor-dashboard-clinics {
            margin-bottom: 8px;
        }

        .doctor-dashboard-clinic-list {
            display: grid;
            gap: 10px;
        }

        .doctor-dashboard-clinic-card {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 15px;
            padding: 17px 19px;
        }

        .doctor-dashboard-clinic-copy {
            display: grid;
            min-width: 0;
            flex: 1 1 auto;
            gap: 3px;
        }

        .doctor-dashboard-clinic-copy h3 {
            margin: 0;
            color: #123f5b;
            font-size: 15px;
        }

        .doctor-dashboard-clinic-copy p {
            margin: 0;
            color: #61748a;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .doctor-dashboard-clinic-copy > span {
            color: #8292a4;
            font-size: 12px;
        }

        .doctor-dashboard-clinics-empty {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 20px;
        }

        .doctor-dashboard-empty-icon {
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
        }

        .doctor-dashboard-clinics-empty h3 {
            margin: 1px 0 4px;
            color: #123f5b;
            font-size: 15px;
        }

        .doctor-dashboard-clinics-empty p {
            margin: 0;
            color: #71849a;
            font-size: 13px;
        }

        .patient-profile-header {
            margin-bottom: 24px;
        }

        .patient-profile-card {
            padding: clamp(22px, 4vw, 34px);
        }

        .patient-profile-section-heading {
            margin-bottom: 22px;
            padding-bottom: 17px;
            border-bottom: 1px solid #edf1f4;
        }

        .patient-profile-section-heading h2 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 20px;
        }

        .patient-profile-section-heading p {
            margin: 0;
            font-size: 14px;
        }

        .patient-profile-form {
            max-width: 760px;
        }

        .clinic-profile-form {
            max-width: 800px;
        }

        .clinic-profile-fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 22px;
        }

        .clinic-profile-field {
            min-width: 0;
        }

        .clinic-profile-field label {
            margin-top: 16px;
        }

        .clinic-profile-field textarea {
            width: 100%;
            min-height: 140px;
            resize: vertical;
        }

        .clinic-profile-wide-field {
            grid-column: 1 / -1;
        }

        .clinic-profile-timezone {
            margin-top: 16px;
            padding: 15px 17px;
            border: 1px solid #e2ebef;
            border-radius: 11px;
            background: #f8fafb;
        }

        .clinic-profile-timezone label {
            margin-top: 0;
        }

        .clinic-profile-timezone input:disabled {
            border-color: #dce5eb;
            background: #eef3f6;
            color: #71849a;
            cursor: not-allowed;
        }

        .patient-profile-fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 22px;
        }

        .patient-profile-field {
            min-width: 0;
        }

        .patient-profile-field label {
            margin-top: 16px;
        }

        .patient-profile-email {
            padding: 15px 17px;
            border: 1px solid #e2ebef;
            border-radius: 11px;
            background: #f8fafb;
        }

        .patient-profile-email label {
            margin: 0 0 7px;
        }

        .patient-profile-email input:disabled {
            border-color: #dce5eb;
            background: #eef3f6;
            color: #71849a;
            cursor: not-allowed;
        }

        .patient-profile-help {
            margin: 7px 0 0;
            color: #71849a;
            font-size: 13px;
        }

        .patient-profile-address {
            grid-column: 1 / -1;
        }

        .patient-profile-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 26px;
            padding-top: 18px;
            border-top: 1px solid #edf1f4;
        }

        .platform-clinic-create-page {
            width: min(100%, 980px);
            margin: 0 auto;
        }

        .platform-clinic-create-header {
            margin-bottom: 24px;
        }

        .platform-clinic-create-card {
            padding: clamp(22px, 4vw, 34px);
        }

        .platform-clinic-section-heading {
            margin-bottom: 22px;
            padding-bottom: 17px;
            border-bottom: 1px solid #edf1f4;
        }

        .platform-clinic-section-heading h2 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 20px;
        }

        .platform-clinic-section-heading p {
            margin: 0;
            font-size: 14px;
        }

        .platform-clinic-form {
            max-width: 800px;
        }

        .platform-clinic-fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 22px;
        }

        .platform-clinic-field {
            min-width: 0;
        }

        .platform-clinic-field label {
            margin-top: 16px;
        }

        .platform-clinic-admin-field {
            grid-column: 1 / -1;
            margin-top: 20px;
            padding: 15px 17px;
            border: 1px solid #e2ebef;
            border-radius: 11px;
            background: #f8fafb;
        }

        .platform-clinic-admin-field label {
            margin: 0 0 7px;
        }

        .platform-clinic-help {
            margin: 7px 0 0;
            color: #71849a;
            font-size: 13px;
        }

        .platform-clinic-address-field {
            grid-column: 1 / -1;
        }

        .platform-clinic-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 26px;
            padding-top: 18px;
            border-top: 1px solid #edf1f4;
        }

        .platform-dashboard-page {
            width: min(100%, 980px);
            margin: 0 auto;
        }

        .platform-dashboard-header {
            margin-bottom: 24px;
        }

        .platform-dashboard-actions {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 10px;
        }

        .platform-resend-invitation-form {
            display: flex;
            max-width: 340px;
            gap: 7px;
            margin-top: 10px;
        }

        .platform-resend-invitation-form input {
            min-width: 0;
            padding: 8px 9px;
            font-size: 12px;
        }

        .platform-resend-invitation-form button {
            padding: 8px 10px;
            font-size: 12px;
            white-space: nowrap;
        }

        .platform-catalog-page {
            width: min(100%, 980px);
            margin: 0 auto;
        }

        .platform-catalog-header {
            margin-bottom: 24px;
        }

        .platform-catalog-section,
        .platform-catalog-lists-section {
            margin-top: 36px;
        }

        .platform-catalog-section-intro {
            margin-bottom: 16px;
        }

        .platform-catalog-section-intro h2 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 22px;
        }

        .platform-catalog-section-intro p {
            margin: 0;
            font-size: 14px;
        }

        .platform-catalog-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: stretch;
            gap: 18px;
        }

        .platform-catalog-card {
            display: flex;
            min-width: 0;
            flex-direction: column;
            padding: 22px;
        }

        .platform-catalog-section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .platform-catalog-section-heading h2 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 20px;
        }

        .platform-catalog-section-heading h3 {
            margin: 0 0 5px;
            color: #123f5b;
            font-size: 18px;
        }

        .platform-catalog-section-heading p {
            margin: 0;
            font-size: 13px;
        }

        .platform-catalog-form > label {
            margin-top: 14px;
        }

        .platform-catalog-form textarea {
            resize: vertical;
        }

        .platform-catalog-submit {
            margin-top: 18px;
        }

        .platform-catalog-list-card {
            padding: 12px 22px 18px;
        }

        .platform-catalog-tabs {
            display: flex;
            gap: 4px;
            border-bottom: 1px solid #e5edf1;
        }

        .platform-catalog-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: -1px;
            padding: 12px 14px;
            border: 0;
            border-bottom: 2px solid transparent;
            border-radius: 8px 8px 0 0;
            background: transparent;
            color: #71849a;
            font: inherit;
            font-size: 14px;
            font-weight: 650;
            cursor: pointer;
        }

        .platform-catalog-tab:hover {
            background: #f4f8fa;
            color: #123f5b;
        }

        .platform-catalog-tab.is-active {
            border-bottom-color: #176b87;
            background: #f4f9fa;
            color: #176b87;
        }

        .platform-catalog-tab:focus-visible {
            outline: 3px solid #176b8740;
            outline-offset: -3px;
        }

        .platform-catalog-tab span {
            display: inline-grid;
            min-width: 21px;
            height: 21px;
            padding: 0 5px;
            place-items: center;
            border-radius: 999px;
            background: #eaf2f5;
            color: #5c7488;
            font-size: 11px;
            font-weight: 700;
        }

        .platform-catalog-tab.is-active span {
            background: #dceef1;
            color: #176b87;
        }

        .platform-catalog-tabpanel[hidden] {
            display: none;
        }

        .platform-catalog-tabpanel {
            min-height: 120px;
        }

        .platform-catalog-items {
            display: grid;
            max-height: 360px;
            gap: 0;
            overflow-y: auto;
            margin-top: 20px;
            border-top: 1px solid #edf1f4;
        }

        .platform-catalog-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-width: 0;
            padding: 12px 2px;
            border-bottom: 1px solid #edf1f4;
        }

        .platform-catalog-item > strong,
        .platform-catalog-item > div > strong {
            color: #344e68;
            font-size: 14px;
            overflow-wrap: anywhere;
        }

        .platform-catalog-item-meta {
            flex: 0 0 auto;
            color: #71849a;
            font-size: 12px;
        }

        .platform-service-item > div {
            min-width: 0;
        }

        .platform-service-item p {
            display: -webkit-box;
            overflow: hidden;
            margin: 4px 0 0;
            color: #71849a;
            font-size: 12px;
            line-height: 1.45;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .platform-catalog-empty-note {
            margin: 0;
            padding: 15px 2px;
            color: #71849a;
            font-size: 13px;
        }

        .platform-doctor-verification {
            margin-top: 0;
        }

        .platform-verification-heading {
            margin-bottom: 15px;
        }

        .platform-verification-heading h2 {
            font-size: 22px;
        }

        .platform-doctor-list {
            display: grid;
            gap: 12px;
        }

        .platform-doctor-card {
            display: grid;
            grid-template-columns: minmax(180px, 1.1fr) minmax(220px, 1fr) auto;
            align-items: center;
            gap: 20px;
            padding: 18px 20px;
        }

        .platform-doctor-identity {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 12px;
        }

        .platform-doctor-avatar {
            display: grid;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            place-items: center;
            border-radius: 13px;
            background: #eaf2f5;
            color: #176b87;
            font-size: 13px;
            font-weight: 750;
        }

        .platform-doctor-identity h3 {
            margin: 0 0 3px;
            color: #123f5b;
            font-size: 15px;
        }

        .platform-doctor-identity p {
            overflow-wrap: anywhere;
            margin: 0;
            color: #71849a;
            font-size: 12px;
        }

        .platform-doctor-credentials {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .platform-doctor-credentials div {
            display: grid;
            min-width: 0;
            gap: 3px;
        }

        .platform-doctor-credentials span {
            color: #8292a4;
            font-size: 11px;
            font-weight: 650;
        }

        .platform-doctor-credentials strong {
            overflow-wrap: anywhere;
            color: #344e68;
            font-size: 12px;
        }

        .platform-verification-empty {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 20px;
        }

        .platform-verification-check {
            display: grid;
            flex: 0 0 40px;
            width: 40px;
            height: 40px;
            place-items: center;
            border-radius: 12px;
            background: #e7f6ef;
            color: #27815f;
            font-size: 20px;
            font-weight: 750;
        }

        .platform-verification-empty h3 {
            margin: 0 0 4px;
            color: #123f5b;
            font-size: 16px;
        }

        .platform-verification-empty p {
            margin: 0;
            font-size: 13px;
        }

        .clinic-description-preview {
            display: -webkit-box;
            overflow: hidden;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            white-space: pre-line;
            color: #61748a;
            line-height: 1.55;
        }

        .clinic-description-preview.is-expanded {
            display: block;
            overflow: visible;
        }

        .clinic-availability-service-description {
            display: -webkit-box;
            overflow: hidden;
            margin: 0;
            color: #61748a;
            line-height: 1.55;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        button.clinic-description-toggle {
            display: inline;
            margin-top: 6px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #176b87;
            font-size: 14px;
            font-weight: 650;
        }

        button.clinic-description-toggle:hover {
            background: transparent;
            color: #123f5b;
        }

        .topbar form {
            margin: 0;
        }

        .container {
            width: min(100% - 40px, 980px);
            margin: 44px auto;
        }

        .narrow {
            width: min(100% - 40px, 480px);
            margin: 56px auto;
        }

        .auth-main {
            display: flex;
            flex-direction: column;
            width: min(100% - 40px, 520px);
            min-height: 100vh;
            margin: 0 auto;
            gap: 16px;
            padding: 28px 0;
            justify-content: center;
        }

        .doctor-invitation-main {
            display: grid;
            width: min(100% - 40px, 820px);
            min-height: 100vh;
            align-content: center;
            justify-items: center;
            margin: 0 auto;
            padding: 18px 0;
        }

        .authenticated-doctor-invitation {
            display: flex;
            height: 100vh;
            height: 100dvh;
            min-height: 100vh;
            min-height: 100dvh;
            flex-direction: column;
        }

        .authenticated-doctor-invitation .topbar {
            flex: 0 0 auto;
        }

        .authenticated-doctor-invitation .doctor-invitation-main {
            flex: 1 1 auto;
            min-height: 0;
        }

        .doctor-invitation-card {
            width: 100%;
            max-width: 780px;
            padding: clamp(22px, 4vw, 34px);
        }

        .doctor-invitation-logo {
            margin-bottom: 7px;
        }

        .doctor-invitation-logo img {
            width: min(120px, 38vw);
        }

        .doctor-invitation-eyebrow {
            display: block;
            margin: 0 0 7px;
            text-align: center;
        }

        .doctor-invitation-heading {
            margin-bottom: 7px;
            font-size: clamp(24px, 3vw, 30px);
        }

        .doctor-invitation-intro {
            margin: 0 auto 14px;
            font-size: 14px;
        }

        .doctor-invitation-message {
            margin: 0 0 12px;
            text-align: center;
            font-size: 14px;
        }

        .doctor-invitation-form {
            width: 100%;
        }

        .doctor-invitation-fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 18px;
        }

        .doctor-invitation-fields > div {
            min-width: 0;
        }

        .doctor-invitation-fields label {
            margin: 10px 0 5px;
            font-size: 13px;
        }

        .doctor-invitation-fields input,
        .doctor-invitation-fields select,
        .doctor-invitation-fields textarea {
            padding: 9px 11px;
            font-size: 14px;
        }

        .doctor-invitation-fields textarea {
            min-height: 41px;
            resize: vertical;
        }

        .doctor-invitation-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 16px;
        }

        .doctor-invitation-single-action {
            display: flex;
            justify-content: center;
            margin-top: 16px;
        }

        .doctor-invitation-decline {
            display: flex;
            justify-content: center;
            margin-top: 15px;
            padding-top: 14px;
            border-top: 1px solid #edf1f4;
        }

        .doctor-invitation-decline button {
            padding: 9px 13px;
            font-size: 13px;
        }

        .doctor-invitation-error {
            text-align: center;
        }

        .auth-card {
            width: 100%;
            padding: clamp(24px, 5vw, 38px);
        }

        .auth-logo {
            margin-bottom: 18px;
            text-align: center;
        }

        .auth-logo img {
            width: min(150px, 48vw);
            height: auto;
        }

        .auth-heading,
        .auth-intro {
            text-align: center;
        }

        .auth-intro {
            margin-bottom: 24px;
        }

        .card {
            background: #fff;
            border: 1px solid #e1e8ef;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 8px 24px #1b3a5710;
        }

        h1 {
            margin: 0 0 8px;
            color: #123f5b;
            font-size: 29px;
            letter-spacing: -.035em;
        }

        h2 {
            margin: 0 0 12px;
            font-size: 18px;
        }

        p {
            color: #61748a;
            line-height: 1.55;
        }

        label {
            display: block;
            margin: 18px 0 7px;
            color: #344e68;
            font-size: 14px;
            font-weight: 650;
        }

        input,
        select,
        textarea {
            display: block;
            width: 100%;
            border: 1px solid #cad5e0;
            border-radius: 8px;
            padding: 11px 12px;
            color: #172b4d;
            background: #fff;
            font: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: 3px solid #176b8728;
            border-color: #176b87;
        }

        button,
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 8px;
            padding: 11px 16px;
            background: #176b87;
            color: #fff;
            font: inherit;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        button:hover,
        .button:hover {
            background: #11566f;
        }

        button:disabled,
        button:disabled:hover {
            background: #d8e0e7;
            color: #718096;
            opacity: 1;
            cursor: default;
        }

        .button.secondary {
            border: 1px solid #c9dce4;
            background: #fff;
            color: #176b87;
        }

        .button.secondary:hover {
            background: #f0f7f9;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 22px;
        }

        .error {
            margin: 5px 0 0;
            color: #a53232;
            font-size: 13px;
        }

        .notice {
            margin-bottom: 20px;
            padding: 13px 16px;
            border-radius: 9px;
            color: #155c48;
            background: #e7f6ef;
        }

        .table-wrap {
            overflow-x: auto;
            margin-top: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th,
        td {
            padding: 13px 10px;
            border-bottom: 1px solid #e8edf2;
            vertical-align: top;
        }

        th {
            color: #61748a;
            font-size: 12px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        td {
            font-size: 14px;
        }

        .muted {
            color: #71849a;
            font-size: 13px;
        }

        .pill {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            background: #edf3f7;
            color: #48627a;
            font-size: 12px;
            font-weight: 700;
        }

        .appointment-status-pill {
            border: 1px solid transparent;
        }

        .appointment-status-scheduled {
            border-color: #f0dfb5;
            background: #fff6df;
            color: #8a5a12;
        }

        .appointment-status-confirmed {
            border-color: #c9e5d5;
            background: #e8f5ee;
            color: #26754f;
        }

        .appointment-status-completed {
            border-color: #ceddf8;
            background: #eaf1ff;
            color: #315ea8;
        }

        .appointment-status-cancelled {
            border-color: #dce3e8;
            background: #edf1f4;
            color: #61748a;
        }

        .stack {
            display: grid;
            gap: 18px;
        }

        .welcome-main {
            width: min(100% - 48px, 1120px);
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: clamp(10px, 2vh, 20px);
            margin: 0 auto;
            padding: clamp(10px, 2vh, 24px) 0;
        }

        body.authenticated-welcome {
            display: flex;
            height: 100vh;
            height: 100dvh;
            min-height: 100vh;
            min-height: 100dvh;
            flex-direction: column;
        }

        .authenticated-welcome .topbar {
            flex: 0 0 auto;
        }

        .authenticated-welcome .welcome-main {
            flex: 1 1 auto;
            height: auto;
            min-height: 0;
        }

        .welcome-hero {
            position: relative;
            overflow: hidden;
            padding: clamp(20px, 3vw, 44px);
            border: 1px solid #dce8ed;
            border-radius: 24px;
            background: linear-gradient(135deg, #fff 24%, #edf7f8 100%);
            box-shadow: 0 16px 44px #1b3a5712;
        }

        .welcome-logo {
            margin-bottom: clamp(16px, 3vh, 40px);
            text-align: center;
        }

        .welcome-logo img {
            width: min(190px, 55vw);
            height: auto;
        }

        .welcome-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(280px, .9fr);
            align-items: center;
            gap: clamp(28px, 5vw, 64px);
        }

        .welcome-eyebrow {
            color: #176b87;
            font-size: 13px;
            font-weight: 750;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .welcome-title {
            max-width: 650px;
            margin-top: 14px;
            font-size: clamp(38px, 5vw, 62px);
            line-height: 1.08;
            letter-spacing: -.045em;
        }

        .welcome-copy>p {
            max-width: 540px;
            margin: 18px 0 26px;
            font-size: 18px;
        }

        .welcome-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .welcome-signin {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 18px;
            color: #71849a;
            font-size: 14px;
        }

        .welcome-signin p {
            margin: 0;
        }

        .welcome-signin a {
            color: #176b87;
            font-weight: 700;
            text-decoration: none;
            transition: color .15s ease;
        }

        .welcome-signin a:hover {
            color: #123f5b;
        }

        .welcome-signin a:focus-visible {
            outline: 3px solid #176b8740;
            outline-offset: 3px;
        }

        .welcome-art {
            position: relative;
            display: grid;
            min-height: clamp(220px, 35vh, 320px);
            place-items: center;
            overflow: hidden;
            border: 1px solid #d3e7e8;
            border-radius: 22px;
            background: radial-gradient(circle at 80% 20%, #fff 0 9%, transparent 9.5%), linear-gradient(145deg, #dff1ef, #eef6f8 58%, #f8e8e2);
        }

        .welcome-art::before,
        .welcome-art::after {
            position: absolute;
            border-radius: 50%;
            content: '';
        }

        .welcome-art::before {
            top: -38px;
            right: -32px;
            width: 150px;
            height: 150px;
            background: #f5c8b850;
        }

        .welcome-art::after {
            bottom: -58px;
            left: -44px;
            width: 190px;
            height: 190px;
            background: #8fcac344;
        }

        .appointment-preview {
            z-index: 1;
            width: min(100% - 40px, 330px);
            padding: 22px;
            border: 1px solid #e4edf0;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 18px 38px #16485b1c;
            transform: rotate(-2deg);
        }

        .preview-heading {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .preview-mark {
            display: grid;
            width: 44px;
            height: 44px;
            place-items: center;
            border-radius: 14px;
            background: #e6f3f3;
            color: #176b87;
            font-size: 24px;
            font-weight: 750;
        }

        .preview-heading strong,
        .preview-heading span {
            display: block;
        }

        .preview-heading strong {
            color: #123f5b;
        }

        .preview-heading span {
            margin-top: 3px;
            color: #71849a;
            font-size: 13px;
        }

        .preview-date {
            padding: 15px;
            border-radius: 12px;
            background: #f4f8fa;
        }

        .preview-date span,
        .preview-date strong {
            display: block;
        }

        .preview-date span {
            color: #71849a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .preview-date strong {
            margin-top: 5px;
            color: #123f5b;
            font-size: 20px;
        }

        .preview-note {
            margin: 15px 0 0;
            color: #557386;
            font-size: 13px;
        }

        .welcome-benefits {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 0;
        }

        .welcome-benefit {
            padding: clamp(14px, 1.8vw, 20px);
            border: 1px solid #e1e8ef;
            border-radius: 16px;
            background: #fff;
        }

        .welcome-benefit-mark {
            display: grid;
            width: 36px;
            height: 36px;
            margin-bottom: 14px;
            place-items: center;
            border-radius: 12px;
            background: #eaf2f5;
            color: #176b87;
            font-size: 18px;
            font-weight: 750;
        }

        .welcome-benefit-mark svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2;
        }

        .welcome-benefit h2 {
            margin-bottom: 6px;
            color: #123f5b;
            font-size: 16px;
        }

        .welcome-benefit p {
            margin: 0;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .doctor-invitation-main {
                width: min(100% - 28px, 480px);
                min-height: 100vh;
                padding: 16px 0;
            }

            .doctor-invitation-card {
                padding: 22px 18px;
            }

            .doctor-invitation-fields {
                grid-template-columns: 1fr;
            }

            .doctor-invitation-actions > button {
                width: 100%;
            }

            .topbar {
                padding: 16px 20px;
            }

            .container {
                margin: 28px auto;
            }

            .card {
                padding: 21px;
            }

            .clinic-directory-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 14px;
                margin-bottom: 20px;
            }

            .clinic-directory-header-actions {
                width: 100%;
                align-items: stretch;
                flex-direction: column;
            }

            .clinic-search-form {
                width: 100%;
                min-width: 0;
            }

            .clinic-directory-card {
                align-items: stretch;
                flex-direction: column;
                gap: 18px;
                padding: 16px;
            }

            .clinic-directory-details {
                align-items: flex-start;
                gap: 14px;
            }

            .clinic-directory-photo {
                flex-basis: 76px;
                width: 76px;
                height: 76px;
                border-radius: 12px;
            }

            .clinic-directory-copy h2 {
                font-size: 17px;
            }

            .clinic-directory-address {
                white-space: normal;
            }

            .clinic-directory-action {
                width: 100%;
            }

            .patient-appointments-header {
                margin-bottom: 24px;
            }

            .patient-appointment-section + .patient-appointment-section {
                margin-top: 30px;
            }

            .patient-section-heading h2 {
                font-size: 19px;
            }

            .patient-appointment-card {
                grid-template-columns: 56px minmax(0, 1fr);
                gap: 13px;
                padding: 15px;
            }

            .patient-appointment-date {
                min-height: 76px;
                border-radius: 11px;
            }

            .patient-appointment-date strong {
                font-size: 24px;
            }

            .patient-appointment-title-row {
                flex-direction: column;
                gap: 8px;
            }

            .patient-appointment-title-row h3 {
                font-size: 17px;
            }

            .patient-appointment-details {
                grid-template-columns: 1fr;
                gap: 9px;
                margin-top: 14px;
            }

            .doctor-appointments-filter-card {
                align-items: stretch;
                flex-direction: column;
                gap: 14px;
                margin-bottom: 22px;
                padding: 17px;
            }

            .doctor-appointments-filter-form {
                align-items: flex-end;
                flex-wrap: wrap;
            }

            .doctor-appointments-filter-form > div {
                flex: 1 1 150px;
            }

            .doctor-appointments-filter-form select {
                width: 100%;
            }

            .doctor-time-off-form-card {
                padding: 18px;
            }

            .doctor-time-off-fields {
                grid-template-columns: 1fr;
            }

            .doctor-time-off-reason-field {
                grid-column: auto;
            }

            .doctor-time-off-form-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .doctor-time-off-form-actions button {
                width: 100%;
            }

            .doctor-time-off-history {
                margin-top: 26px;
            }

            .doctor-time-off-item {
                align-items: flex-start;
                flex-wrap: wrap;
                gap: 11px;
                padding: 15px;
            }

            .doctor-time-off-item-content {
                flex-basis: calc(100% - 56px);
            }

            .doctor-time-off-item-heading {
                align-items: flex-start;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 10px;
            }

            .doctor-time-off-remove-form {
                margin-left: 55px;
            }

            .patient-appointment-footer {
                align-items: flex-start;
                flex-direction: column;
                gap: 13px;
            }

            .patient-appointment-footer > .button {
                width: 100%;
            }

            .patient-cancel-details {
                width: 100%;
            }

            .patient-cancel-details form {
                width: 100%;
            }

            .patient-history-card {
                padding: 6px 12px;
            }

            .patient-profile-header {
                margin-bottom: 18px;
            }

            .clinic-dashboard-welcome {
                align-items: stretch;
                flex-direction: column;
                gap: 20px;
                padding: 22px;
            }

            .clinic-dashboard-profile-link {
                width: 100%;
            }

            .clinic-dashboard-workspace {
                margin-top: 26px;
            }

            .clinic-dashboard-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .clinic-appointments-header {
                align-items: stretch;
            }

            .clinic-appointments-clock {
                min-width: 0;
                text-align: left;
            }

            .clinic-appointments-filter-card {
                align-items: stretch;
                flex-direction: column;
                gap: 15px;
                padding: 18px;
            }

            .clinic-appointments-filter-form {
                align-items: flex-end;
                flex-wrap: wrap;
            }

            .clinic-appointments-filter-form > div {
                flex: 1 1 155px;
            }

            .clinic-appointments-filter-form select {
                width: 100%;
            }

            .clinic-appointment-card {
                padding: 16px;
            }

            .clinic-appointment-card-main {
                grid-template-columns: 54px minmax(0, 1fr);
                gap: 12px;
            }

            .clinic-appointment-date-block {
                min-height: 76px;
            }

            .clinic-appointment-heading-row {
                flex-direction: column;
                gap: 9px;
            }

            .clinic-appointment-meta {
                grid-template-columns: 1fr;
                gap: 8px;
                margin-top: 13px;
            }

            .clinic-appointment-actions {
                align-items: stretch;
                flex-direction: column;
                gap: 10px;
            }

            .clinic-appointment-actions > form button {
                width: 100%;
            }

            .clinic-appointment-cancel form {
                width: 100%;
            }

            .clinic-workspace-header {
                margin-bottom: 18px;
            }

            .clinic-workspace-card,
            .clinic-doctor-invite-card,
            .clinic-doctor-schedule-card {
                padding: 18px;
            }

            .clinic-workspace-section {
                margin-top: 26px;
            }

            .clinic-workspace-section-heading {
                align-items: flex-start;
            }

            .clinic-workspace-section-heading h2 {
                font-size: 18px;
            }

            .clinic-service-grid {
                grid-template-columns: 1fr;
                gap: 9px;
            }

            .clinic-workspace-form-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .clinic-workspace-action-buttons {
                width: 100%;
            }

            .clinic-workspace-action-buttons > .button,
            .clinic-workspace-action-buttons > button {
                flex: 1;
                width: 100%;
            }

            .clinic-schedule-guidance {
                margin-top: 0;
                padding: 12px;
            }

            .clinic-doctor-schedule-header {
                flex-wrap: wrap;
            }

            .clinic-doctor-schedule-header .clinic-workspace-count {
                margin-left: 57px;
            }

            .clinic-schedule-item {
                align-items: flex-start;
                flex-direction: column;
            }

            .clinic-schedule-actions {
                width: 100%;
                justify-content: space-between;
            }

            .clinic-schedule-form-grid,
            .clinic-schedule-add-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .clinic-schedule-add-section {
                padding: 15px;
            }

            .clinic-schedule-submit-wrap {
                grid-column: 1 / -1;
            }

            .clinic-schedule-submit-wrap button {
                width: 100%;
            }

            .clinic-doctor-invite-form {
                align-items: stretch;
                flex-direction: column;
            }

            .clinic-doctor-invite-form button {
                width: 100%;
            }

            .clinic-doctor-team-card {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px 12px;
                padding: 15px;
            }

            .clinic-doctor-team-card > .clinic-doctor-schedule-avatar {
                grid-column: 1;
                grid-row: 1;
            }

            .clinic-doctor-team-identity {
                grid-column: 2;
                grid-row: 1;
            }

            .clinic-doctor-team-detail:last-child {
                grid-column: 1 / -1;
            }

            .clinic-invitation-list {
                padding-right: 14px;
                padding-left: 14px;
            }

            .clinic-invitation-row {
                align-items: flex-start;
            }

            .doctor-dashboard-welcome {
                align-items: stretch;
                flex-direction: column;
                gap: 20px;
                padding: 22px;
            }

            .doctor-verification-card {
                flex-basis: auto;
            }

            .doctor-dashboard-section {
                margin-top: 26px;
            }

            .doctor-dashboard-actions-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .doctor-dashboard-action-card {
                min-height: 108px;
                gap: 12px;
                padding: 17px;
            }

            .doctor-dashboard-clinic-card {
                gap: 11px;
                padding: 15px;
            }

            .doctor-dashboard-clinic-card > .pill {
                align-self: flex-start;
            }

            .clinic-dashboard-card {
                min-height: 108px;
                gap: 12px;
                padding: 17px;
            }

            .patient-profile-fields {
                grid-template-columns: 1fr;
            }

            .clinic-profile-fields {
                grid-template-columns: 1fr;
            }

            .patient-profile-address {
                grid-column: auto;
            }

            .clinic-profile-wide-field {
                grid-column: auto;
            }

            .patient-profile-actions {
                justify-content: stretch;
            }

            .patient-profile-actions > .button,
            .patient-profile-actions > button {
                flex: 1;
            }

            .platform-clinic-create-header {
                margin-bottom: 18px;
            }

            .platform-clinic-fields {
                grid-template-columns: 1fr;
            }

            .platform-clinic-admin-field {
                grid-column: auto;
            }

            .platform-clinic-address-field {
                grid-column: auto;
            }

            .platform-clinic-form-actions {
                justify-content: stretch;
            }

            .platform-clinic-form-actions > .button,
            .platform-clinic-form-actions > button {
                flex: 1;
            }

            .platform-dashboard-header {
                margin-bottom: 20px;
            }

            .platform-dashboard-actions {
                width: 100%;
                align-items: stretch;
                flex-direction: column;
            }

            .platform-dashboard-actions > .button {
                width: 100%;
            }

            .platform-catalog-header {
                margin-bottom: 20px;
            }

            .platform-doctor-verification {
                margin-top: 0;
            }

            .platform-catalog-section,
            .platform-catalog-lists-section {
                margin-top: 28px;
            }

            .platform-catalog-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .platform-catalog-card {
                padding: 18px;
            }

            .platform-catalog-list-card {
                padding: 10px 15px 15px;
            }

            .platform-catalog-items {
                max-height: 300px;
            }

            .platform-verification-heading {
                align-items: flex-start;
            }

            .platform-doctor-card {
                grid-template-columns: 1fr;
                gap: 15px;
                padding: 16px;
            }

            .platform-doctor-card > form button {
                width: 100%;
            }

            h1 {
                font-size: 25px;
            }

            .welcome-main {
                width: min(100% - 28px, 1120px);
                gap: 12px;
                padding: 10px 0;
            }

            .welcome-hero {
                padding: 18px;
                border-radius: 18px;
            }

            .welcome-logo {
                margin-bottom: 14px;
            }

            .welcome-logo img {
                width: min(150px, 45vw);
            }

            .welcome-grid {
                grid-template-columns: 1fr;
            }

            .welcome-art {
                min-height: clamp(130px, 18vh, 180px);
            }

            .appointment-preview {
                padding: 14px;
            }

            .preview-heading {
                margin-bottom: 10px;
            }

            .preview-mark {
                width: 34px;
                height: 34px;
            }

            .preview-date {
                padding: 10px;
            }

            .preview-date strong {
                font-size: 17px;
            }

            .preview-note {
                margin-top: 8px;
            }

            .welcome-copy>p {
                margin: 10px 0 16px;
                font-size: 16px;
            }

            .welcome-title {
                margin-top: 8px;
                font-size: clamp(32px, 8vw, 42px);
            }

            .welcome-benefits {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 8px;
            }

            .welcome-benefit {
                padding: 10px 6px;
                text-align: center;
            }

            .welcome-benefit-mark {
                width: 28px;
                height: 28px;
                margin: 0 auto 7px;
                font-size: 15px;
            }

            .welcome-benefit h2 {
                font-size: 12px;
                line-height: 1.2;
            }

            .welcome-benefit p {
                font-size: 11px;
                line-height: 1.3;
            }
        }

        @media (max-height: 700px) {
            .welcome-main {
                gap: 8px;
                padding: 8px 0;
            }

            .welcome-hero {
                padding: 14px;
            }

            .welcome-logo {
                margin-bottom: 12px;
            }

            .welcome-logo img {
                width: min(140px, 40vw);
            }

            .welcome-grid {
                gap: 14px;
            }

            .welcome-art {
                min-height: 105px;
            }

            .appointment-preview {
                padding: 10px;
            }

            .preview-heading {
                margin-bottom: 6px;
            }

            .preview-mark {
                width: 28px;
                height: 28px;
                font-size: 18px;
            }

            .preview-date {
                padding: 8px;
            }

            .preview-date strong {
                font-size: 15px;
            }

            .preview-note {
                margin-top: 5px;
                font-size: 12px;
            }

            .welcome-copy>p {
                margin-top: 8px;
                margin-bottom: 10px;
                font-size: 14px;
            }

            .welcome-title {
                font-size: clamp(30px, 7vw, 38px);
            }

            .welcome-benefit {
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .welcome-benefit-mark {
                margin-bottom: 6px;
            }

            .welcome-benefit p {
                display: -webkit-box;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
            }
        }
    </style>
</head>

<body @if (($mainClass ?? null) === 'welcome-main' && auth()->check()) class="authenticated-welcome" @elseif (($mainClass ?? null) === 'doctor-invitation-main' && auth()->check()) class="authenticated-doctor-invitation" @endif>
    @auth
        <header class="topbar">
            <a class="brand" href="{{ route('home') }}">CareOn</a>
            <div class="nav-links">
                @if (auth()->user()->role === 'patient')
                    <a href="{{ route('clinics.index') }}">Find a clinic</a>
                    <a href="{{ route('appointments.index') }}">Appointments</a>
                    <a href="{{ route('patient.profile.edit') }}">Profile</a>
                @else
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button secondary" type="submit">Log out</button>
                </form>
            </div>
        </header>
    @endauth
    <main class="{{ $mainClass ?? 'container' }}">
        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="notice" style="color:#8e2e2e;background:#fff0ed">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </main>
</body>

</html>
