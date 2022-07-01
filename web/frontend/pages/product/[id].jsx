import { Card, DataTable, Page, Layout, SkeletonBodyText } from '@shopify/polaris';
import { Loading, TitleBar } from '@shopify/app-bridge-react';
import { useAppQuery } from '../../hooks';
import { useParams } from 'react-router-dom';

export default function ProductDetail() {
    const { id } = useParams();

    const {
        data,
        isLoading,
        isRefetching
    } = useAppQuery({
        url: `/api/product/${id}`
    })

    const breadcrumbs = [{content: 'Customers and Products', url: '/' }];

    /*
     These are mock values.
     Set isLoading to false to preview the page without loading markup.
  */
    // const isLoading = false;
    // const isRefetching = false;
    // const Product = {
    //     title: 'High Priest',
    //     vendor: 'Vendor',
    //     type: 'Religious Leader',
    //     price: 88,
    //     variants: [
    //         {
    //             title: 'Holy',
    //             vendor: 'Vendor',
    //             type: 'Religious Leader',
    //             price: 88
    //         },
    //         {
    //             title: 'Disciple',
    //             vendor: 'Vendor',
    //             type: 'Religious Leader',
    //             price: 88
    //         }
    //     ]
    // };

    if (isLoading || isRefetching) {
        return (
            <Page>
                <TitleBar title="Product Display" breadcrumbs={breadcrumbs}
                primaryAction={null} />
                <Loading />
                <Layout>
                    <Layout.Section>
                        <Card sectioned title="Product">
                            <SkeletonBodyText />
                        </Card>
                    </Layout.Section>
                </Layout>
            </Page>
        )
    }
    // const rows = [
    //     [
    //         "Holy",
    //         "Vendor",
    //         "Religious Leader",
    //         10
    //     ],
    // ];

    console.log(data);

    let rows = [];

    data.forEach((dataPiece) => {
        rows.push([
            dataPiece.title,
            dataPiece.vendor,
            dataPiece.type,
            dataPiece.price
        ])
    });

    return (
        <Page>
            <TitleBar title="Product Title" breadcrumbs={breadcrumbs}
                primaryAction={null} />
            <DataTable
                columnContentTypes = {[
                    "text",
                    "text",
                    "text",
                    "numeric",
                ]}
                headings={[
                    "Title",
                    "Vendor",
                    "Type",
                    "Price",
                ]}
                rows={rows}
                loading={isLoading}
            />

        </Page>
        
    )
}