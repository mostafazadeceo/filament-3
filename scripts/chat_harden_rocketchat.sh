#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

container="${1:-filament-3-mongo-1}"

read -r -d '' JS <<'EOF' || true
db = db.getSiblingDB("rocketchat");

const settingsUpdates = {
  Accounts_ShowFormLogin: false,
  Accounts_AllowUserRegistration: false,
  Accounts_RegistrationForm: "Disabled",
  Accounts_AllowPasswordChange: true,
  Accounts_AllowPasswordChangeForOAuthUsers: true,
  Layout_Login_Hide_Powered_By: true,
  Layout_Login_Hide_Title: true,
  Layout_Login_Hide_Logo: true,
  Site_Name: "Abrak Chat",
};

for (const [key, value] of Object.entries(settingsUpdates)) {
  db.rocketchat_settings.updateOne(
    { _id: key },
    { $set: { value, _updatedAt: new Date() } },
    { upsert: true },
  );
}

const customScript = `(() => {
  const target = "https://abrak.org";
  const blocked = ["/setup-wizard", "/register", "/signup", "/oauth/error"];
  const path = (window.location && window.location.pathname) || "";
  if (blocked.some((p) => path.startsWith(p))) {
    window.location.replace(target);
    return;
  }

  const replaceBranding = () => {
    const body = document.body;
    if (!body) return;

    const walker = document.createTreeWalker(body, NodeFilter.SHOW_TEXT);
    while (walker.nextNode()) {
      const node = walker.currentNode;
      if (!node || !node.nodeValue) continue;
      node.nodeValue = node.nodeValue
        .replace(/Rocket\\.Chat/gi, "Abrak Chat")
        .replace(/WordPress/gi, "Abrak SSO")
        .replace(/Odoo/gi, "Abrak")
        .replace(/ودوو/gi, "ابرک");
    }

    // Only rewrite external vendor links. Do not touch local OAuth routes such as
    // /oauth/authorize?service=abrak, otherwise SSO flow breaks.
    document.querySelectorAll("a[href]").forEach((a) => {
      const href = (a.getAttribute("href") || "").trim();
      if (href === "" || href.startsWith("/") || href.startsWith("#")) {
        return;
      }

      try {
        const url = new URL(href, window.location.origin);
        const host = (url.hostname || "").toLowerCase();
        const isVendorHost = host.includes("rocket.chat")
          || host.includes("odoo.com");

        if (isVendorHost) {
          a.setAttribute("href", target);
        }
      } catch (_) {
        // Ignore malformed links.
      }
    });
  };

  replaceBranding();
  setInterval(replaceBranding, 1500);
})();`;

db.rocketchat_settings.updateOne(
  { _id: "Custom_Script_Logged_Out" },
  { $set: { value: customScript, _updatedAt: new Date() } },
  { upsert: true },
);

db.rocketchat_settings.updateOne(
  { _id: "Custom_Script_Logged_In" },
  { $set: { value: customScript, _updatedAt: new Date() } },
  { upsert: true },
);

const customTranslations = {
  en: {
    Rocket_Chat: "Abrak Chat",
    Odoo: "Abrak",
  },
  fa: {
    Rocket_Chat: "چت ابرک",
    Odoo: "ابرک",
  },
};

db.rocketchat_settings.updateOne(
  { _id: "Custom_Translations" },
  { $set: { value: JSON.stringify(customTranslations), _updatedAt: new Date() } },
  { upsert: true },
);

db.meteor_accounts_loginServiceConfiguration.updateOne(
  { service: "abrak" },
  {
    $set: {
      loginStyle: "redirect",
      showButton: true,
      buttonLabelText: "Abrak SSO",
      buttonColor: "#f59e0b",
      buttonLabelColor: "#111827",
    },
  },
);

function addRole(permission, role) {
  db.rocketchat_permissions.updateOne(
    { _id: permission, roles: { $ne: role } },
    { $push: { roles: role } },
  );
}

addRole("create-c", "owner");
addRole("create-p", "owner");

db.users.updateMany(
  { roles: "owner", roles: { $ne: "user" } },
  { $push: { roles: "user" } },
);

printjson({
  setupWizard: db.rocketchat_settings.findOne({ _id: "Setup_Wizard" })?.value,
  accountsShowFormLogin: db.rocketchat_settings.findOne({ _id: "Accounts_ShowFormLogin" })?.value,
  accountsAllowUserRegistration: db.rocketchat_settings.findOne({ _id: "Accounts_AllowUserRegistration" })?.value,
  oauthService: db.meteor_accounts_loginServiceConfiguration.findOne({ service: "abrak" }),
  createC: db.rocketchat_permissions.findOne({ _id: "create-c" })?.roles,
  createP: db.rocketchat_permissions.findOne({ _id: "create-p" })?.roles,
});
EOF

docker exec -i "$container" mongosh --quiet --eval "$JS"
