import { Link, Card, DataTable } from "@shopify/polaris";
// import React from "react";

export function ProductCard({ Products, loading}) {
    const rows = [
        [
            <Link
                removeUnderline
                url="https://dckaptraining.myshopify.com/admin/apps/php-training/product/high-priest"
            >
                High Priest
            </Link>,
            "Vendor",
            "Religious Leader",
            10,
            "false"
        ],
    ];

    return (
        <Card>
            <DataTable
                columnContentTypes = {[
                    "text",
                    "text",
                    "text",
                    "numeric",
                    "text"
                ]}
                headings={[
                    "Title",
                    "Vendor",
                    "Type",
                    "Price",
                    "Has only default variant"
                ]}
                rows={rows}
                loading={loading}
            />
        </Card>
    )
}