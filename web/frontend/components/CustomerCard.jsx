import { Card, DataTable } from "@shopify/polaris";
// import React from "react";

export function CustomerCard({ Customers, loading}) {
    const rows = [
        ["Seth", "Rowland", "sethr@dckap.com", 6, 60.78],
        ["Santhosh", "R", "santhoshr@dckap.com", 8, 21.89]
    ];

    return (
        <Card>
            <DataTable
                columnContentTypes = {[
                    "text",
                    "text",
                    "text",
                    "numeric",
                    "numeric"
                ]}
                headings={[
                    "First name",
                    "Last name",
                    "Email",
                    "Number of orders",
                    "Net sales"
                ]}
                rows={rows}
                loading={loading}
            />
        </Card>
    )
}